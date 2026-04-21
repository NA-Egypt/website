<?php

namespace App\Livewire;

use Livewire\Component;

class YearlyCalendar extends Component
{
    public $eventId;
    public $title;
    public $start;
    public $end;
    public $description;
    public $color = '#3788d8'; // Default FullCalendar blue
    public $organizer;
    public $location;

    protected $rules = [
        'title' => 'required|string|max:255',
        'start' => 'required|date',
        'end' => 'required|date|after_or_equal:start',
        'description' => 'nullable|string',
        'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        'organizer' => 'nullable|string|max:255',
        'location' => 'nullable|string|max:255',
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
                        'organizer' => $event->organizer,
                        'location' => $event->location,
                    ],
                ];
            });
    }

    public function saveEvent()
    {
        if (!$this->checkAuth()) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $this->validate();

        if ($this->eventId) {
            $event = \App\Models\CalendarEvent::findOrFail($this->eventId);
            if ($event->user_id !== auth()->id() && !auth()->user()->hasRole(['super admin', 'Committees'])) {
                 abort(403, 'Unauthorized action.');
            }
            $event->update([
                'title' => $validatedData['title'],
                'start' => $validatedData['start'],
                'end' => $validatedData['end'],
                'description' => $validatedData['description'],
                'color' => $validatedData['color'],
                'organizer' => $validatedData['organizer'] ?? null,
                'location' => $validatedData['location'] ?? null,
            ]);
        } else {
            \App\Models\CalendarEvent::create([
                'title' => $validatedData['title'],
                'start' => $validatedData['start'],
                'end' => $validatedData['end'],
                'description' => $validatedData['description'],
                'color' => $validatedData['color'],
                'organizer' => $validatedData['organizer'] ?? null,
                'location' => $validatedData['location'] ?? null,
                'user_id' => auth()->id(),
            ]);
        }

        $this->reset(['eventId', 'title', 'start', 'end', 'description', 'color', 'organizer', 'location']);
        $this->dispatch('event-saved');
    }

    public function editEvent($id)
    {
        if (!$this->checkAuth()) {
            abort(403, 'Unauthorized action.');
        }

        $event = \App\Models\CalendarEvent::findOrFail($id);
        
        $this->eventId = $event->id;
        $this->title = $event->title;
        // Format to YYYY-MM-DDTHH:MM which datetime-local expects
        $this->start = $event->start->format('Y-m-d\TH:i');
        $this->end = $event->end->format('Y-m-d\TH:i');
        $this->description = $event->description;
        $this->color = $event->color;
        $this->organizer = $event->organizer;
        $this->location = $event->location;

        $this->dispatch('open-modal');
    }

    public function deleteEvent()
    {
        if (!$this->checkAuth()) {
            abort(403, 'Unauthorized action.');
        }

        if ($this->eventId) {
            $event = \App\Models\CalendarEvent::findOrFail($this->eventId);
            if ($event->user_id !== auth()->id() && !auth()->user()->hasRole(['super admin', 'Committees'])) {
                 abort(403, 'Unauthorized action.');
            }
            $event->delete();
        }

        $this->reset(['eventId', 'title', 'start', 'end', 'description', 'color', 'organizer', 'location']);
        $this->dispatch('event-saved');
    }

    public function checkAuth()
    {
        return auth()->check() && (auth()->user()->hasPermissionTo('can_manage_calendar') || auth()->user()->hasRole(['super admin', 'Committees']));
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
                'organizer' => $event->organizer,
                'location' => $event->location,
            ];
        });

        return view('livewire.yearly-calendar', ['events' => $events])
            ->layout('components.layout');
    }
}
