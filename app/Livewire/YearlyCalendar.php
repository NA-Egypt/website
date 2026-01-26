<?php

namespace App\Livewire;

use Livewire\Component;

class YearlyCalendar extends Component
{
    public $title;
    public $start;
    public $end;
    public $description;
    public $color = '#3788d8'; // Default FullCalendar blue

    protected $rules = [
        'title' => 'required|string|max:255',
        'start' => 'required|date',
        'end' => 'required|date|after_or_equal:start',
        'description' => 'nullable|string',
        'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ];

    public function fetchEvents($start, $end)
    {
        return \App\Models\CalendarEvent::query()
            ->where('start', '>=', $start)
            ->where('end', '<=', $end)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start->toIso8601String(),
                    'end' => $event->end->toIso8601String(),
                    'color' => $event->color,
                    'extendedProps' => [
                        'description' => $event->description,
                        'user_id' => $event->user_id,
                    ],
                ];
            });
    }

    public function saveEvent()
    {
        if (!auth()->check() || !auth()->user()->hasPermission('can_manage_calendar')) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $this->validate();

        \App\Models\CalendarEvent::create([
            'title' => $validatedData['title'],
            'start' => $validatedData['start'],
            'end' => $validatedData['end'],
            'description' => $validatedData['description'],
            'color' => $validatedData['color'],
            'user_id' => auth()->id(),
        ]);

        $this->reset(['title', 'start', 'end', 'description', 'color']);
        $this->dispatch('event-saved');
    }

    public function checkAuth()
    {
        return auth()->check() && auth()->user()->hasPermission('can_manage_calendar');
    }

    public function render()
    {
        // Fetch all events for initial load
        $events = \App\Models\CalendarEvent::all()->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start->toIso8601String(),
                'end' => $event->end->toIso8601String(),
                'color' => $event->color,
                'description' => $event->description,
            ];
        });

        return view('livewire.yearly-calendar', ['events' => $events])
            ->layout('components.layout');
    }
}
