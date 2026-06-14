<?php

namespace App\Livewire;

use Livewire\Component;
use App\Traits\EventRecurrenceTrait;

class YearlyCalendar extends Component
{
    use EventRecurrenceTrait;
    public $eventId;
    public $title;
    public $start;
    public $end;
    public $description;
    public $color = '#3788d8'; // Default FullCalendar blue
    public $organizer;
    public $location;
    public $recurrence = ['once'];
    public $is_featured = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'start' => 'required|date',
        'end' => 'required|date|after_or_equal:start',
        'description' => 'nullable|string',
        'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        'organizer' => 'nullable|string|max:255',
        'location' => 'nullable|string|max:255',
        'recurrence' => 'nullable|array',
        'is_featured' => 'nullable|boolean',
    ];

    public function fetchEvents($start, $end)
    {
        $baseEvents = \App\Models\CalendarEvent::query()
            ->where('start', '<=', $end) // Allow recurring events that started in the past
            ->get();
            
        $expandedEvents = collect();

        foreach ($baseEvents as $event) {
            $instances = $this->generateOccurrences($event, $start, $end);
            foreach ($instances as $instance) {
                $expandedEvents->push([
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $instance['start']->toIso8601String(),
                    'end' => $instance['end']->toIso8601String(),
                    'color' => $event->color,
                    'extendedProps' => [
                        'description' => $event->description,
                        'user_id' => $event->user_id,
                        'organizer' => $event->organizer,
                        'location' => $event->location,
                        'recurrence' => $event->recurrence,
                    ],
                ]);
            }
        }
        
        return $expandedEvents;
    }

    public function saveEvent()
    {
        if (!$this->checkAuth()) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $this->validate();

        if ($this->eventId) {
            $event = \App\Models\CalendarEvent::findOrFail($this->eventId);
            if (!$this->isRsc() && $event->user_id !== auth()->id()) {
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
                'recurrence' => $validatedData['recurrence'] ?? ['once'],
                'is_featured' => $validatedData['is_featured'] ?? false,
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
                'recurrence' => $validatedData['recurrence'] ?? ['once'],
                'is_featured' => $validatedData['is_featured'] ?? false,
            ]);
        }

        $this->reset(['eventId', 'title', 'start', 'end', 'description', 'color', 'organizer', 'location', 'recurrence', 'is_featured']);
        $this->dispatch('event-saved');
    }

    public function editEvent($id)
    {
        if (!$this->checkAuth()) {
            abort(403, 'Unauthorized action.');
        }

        $event = \App\Models\CalendarEvent::findOrFail($id);
        
        if (!$this->isRsc() && $event->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $this->eventId = $event->id;
        $this->title = $event->title;
        // Format to YYYY-MM-DDTHH:MM which datetime-local expects
        $this->start = $event->start->format('Y-m-d\TH:i');
        $this->end = $event->end->format('Y-m-d\TH:i');
        $this->description = $event->description;
        $this->color = $event->color;
        $this->organizer = $event->organizer;
        $this->location = $event->location;
        $this->recurrence = $event->recurrence ?? ['once'];
        $this->is_featured = (bool) $event->is_featured;

        $this->dispatch('open-modal');
    }

    public function deleteEvent()
    {
        if (!$this->checkAuth()) {
            abort(403, 'Unauthorized action.');
        }

        if ($this->eventId) {
            $event = \App\Models\CalendarEvent::findOrFail($this->eventId);
            if (!$this->isRsc() && $event->user_id !== auth()->id()) {
                 abort(403, 'Unauthorized action.');
            }
            $event->delete();
        }

        $this->reset(['eventId', 'title', 'start', 'end', 'description', 'color', 'organizer', 'location', 'recurrence', 'is_featured']);
        $this->dispatch('event-saved');
    }

    public function resetModal()
    {
        $this->reset(['eventId', 'title', 'start', 'end', 'description', 'color', 'organizer', 'location', 'recurrence', 'is_featured']);
    }

    public function selectDateRange($start, $end)
    {
        if (!$this->checkAuth()) {
            abort(403, 'Unauthorized action.');
        }

        $this->resetModal();

        $startStr = $start;
        if (!str_contains($startStr, 'T')) {
            $startStr .= 'T09:00';
        }

        $endStr = $end;
        if (!str_contains($endStr, 'T')) {
            // FullCalendar date-only selection ends are exclusive. If they selected a single day,
            // the difference is 1 day. In this case we set the end time on the same day.
            $startDate = \Carbon\Carbon::parse($start);
            $endDate = \Carbon\Carbon::parse($end);
            if ((int) $endDate->diffInDays($startDate, true) === 1) {
                $endStr = $startDate->format('Y-m-d') . 'T10:00';
            } else {
                $endStr .= 'T10:00';
            }
        }

        $this->start = \Carbon\Carbon::parse($startStr)->format('Y-m-d\TH:i');
        $this->end = \Carbon\Carbon::parse($endStr)->format('Y-m-d\TH:i');

        $this->dispatch('open-modal');
    }

    public function isRsc()
    {
        if (!auth()->check()) {
            return false;
        }
        $user = auth()->user();
        return $user->hasRole('super admin') || $user->hasRole('rsc');
    }

    public function checkAuth()
    {
        if (!auth()->check()) {
            return false;
        }
        $user = auth()->user();
        return $user->hasPermissionTo('can_manage_calendar') || 
               $user->hasRole('super admin') ||
               $user->hasRole('Committees') ||
               $user->hasRole('ServiceBody') ||
               $this->isRsc();
    }

    public function render()
    {
        // Initial load for current year (or roughly visible window)
        $start = \Carbon\Carbon::now()->startOfYear()->toIso8601String();
        $end = \Carbon\Carbon::now()->endOfYear()->toIso8601String();
        
        $events = $this->fetchEvents($start, $end);

        return view('livewire.yearly-calendar', ['events' => $events])
            ->layout('components.layout');
    }
}
