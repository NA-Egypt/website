<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CalendarEvent;
use App\Traits\EventRecurrenceTrait;
use Illuminate\Validation\Rule;

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
    public $is_hero = false;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'organizer' => ['required', 'string', 'max:255', Rule::in(CalendarEvent::getValidOrganizerNames())],
            'location' => 'nullable|string|max:255',
            'recurrence' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'is_hero' => 'nullable|boolean',
        ];
    }

    public function fetchEvents($start, $end)
    {
        $baseEvents = CalendarEvent::query()
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
                        'location_info' => $event->location_link_info,
                        'recurrence' => $event->recurrence,
                        'is_featured' => (bool) $event->is_featured,
                        'is_hero' => (bool) $event->is_hero,
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
        $canManageHero = $this->isRsc() || (auth()->check() && auth()->user()->can('manage calendar events'));

        if ($this->eventId) {
            $event = CalendarEvent::findOrFail($this->eventId);
            if (!$this->isRsc() && $event->user_id !== auth()->id()) {
                 abort(403, 'Unauthorized action.');
            }
            $updateData = [
                'title' => $validatedData['title'],
                'start' => $validatedData['start'],
                'end' => $validatedData['end'],
                'description' => $validatedData['description'],
                'color' => $validatedData['color'],
                'organizer' => $validatedData['organizer'],
                'location' => $validatedData['location'] ?? null,
                'recurrence' => $validatedData['recurrence'] ?? ['once'],
                'is_featured' => $validatedData['is_featured'] ?? false,
            ];
            if ($canManageHero) {
                $updateData['is_hero'] = (bool) ($validatedData['is_hero'] ?? false);
            }
            $event->update($updateData);
        } else {
            CalendarEvent::create([
                'title' => $validatedData['title'],
                'start' => $validatedData['start'],
                'end' => $validatedData['end'],
                'description' => $validatedData['description'],
                'color' => $validatedData['color'],
                'organizer' => $validatedData['organizer'],
                'location' => $validatedData['location'] ?? null,
                'user_id' => auth()->id(),
                'recurrence' => $validatedData['recurrence'] ?? ['once'],
                'is_featured' => $validatedData['is_featured'] ?? false,
                'is_hero' => $canManageHero ? (bool) ($validatedData['is_hero'] ?? false) : false,
            ]);
        }

        $this->reset(['eventId', 'title', 'start', 'end', 'description', 'color', 'organizer', 'location', 'recurrence', 'is_featured', 'is_hero']);
        $this->dispatch('event-saved');
    }

    public function editEvent($id)
    {
        if (!$this->checkAuth()) {
            abort(403, 'Unauthorized action.');
        }

        $event = CalendarEvent::findOrFail($id);
        
        if (!$this->isRsc() && $event->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $this->eventId = $event->id;
        $this->title = $event->title;
        $this->start = $event->start->format('Y-m-d\TH:i');
        $this->end = $event->end->format('Y-m-d\TH:i');
        $this->description = $event->description;
        $this->color = $event->color;
        $this->organizer = $event->organizer;
        $this->location = $event->location;
        $this->recurrence = $event->recurrence ?? ['once'];
        $this->is_featured = (bool) $event->is_featured;
        $this->is_hero = (bool) $event->is_hero;

        $this->dispatch('open-modal');
    }

    public function deleteEvent()
    {
        if (!$this->checkAuth()) {
            abort(403, 'Unauthorized action.');
        }

        if ($this->eventId) {
            $event = CalendarEvent::findOrFail($this->eventId);
            if (!$this->isRsc() && $event->user_id !== auth()->id()) {
                 abort(403, 'Unauthorized action.');
            }
            $event->delete();
        }

        $this->reset(['eventId', 'title', 'start', 'end', 'description', 'color', 'organizer', 'location', 'recurrence', 'is_featured', 'is_hero']);
        $this->dispatch('event-saved');
    }

    public function resetModal()
    {
        $this->reset(['eventId', 'title', 'start', 'end', 'description', 'color', 'organizer', 'location', 'recurrence', 'is_featured', 'is_hero']);
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
        return $user->can('create calendar events') ||
               $user->can('can_manage_calendar') ||
               $user->hasRole('super admin') ||
               $user->hasRole('rsc') ||
               $user->hasRole('Committees') ||
               $user->hasRole('ServiceBody');
    }

    public function render()
    {
        $start = \Carbon\Carbon::now()->startOfYear()->toIso8601String();
        $end = \Carbon\Carbon::now()->endOfYear()->toIso8601String();
        
        $events = $this->fetchEvents($start, $end);
        $organizerCatalog = CalendarEvent::getOrganizerCatalog(auth()->user(), app()->getLocale());

        return view('livewire.yearly-calendar', [
            'events' => $events,
            'organizerGroups' => $organizerCatalog['groups'],
            'defaultOrganizer' => $organizerCatalog['default_organizer'],
        ])->layout('components.layout');
    }
}
