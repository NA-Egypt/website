<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\User;
use App\Http\Resources\CalendarEventResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CalendarEventController extends Controller
{
    use \App\Traits\EventRecurrenceTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startStr = $request->query('start');
        $endStr = $request->query('end');

        if ($startStr && $endStr) {
            $windowStart = \Carbon\Carbon::parse($startStr);
            $windowEnd = \Carbon\Carbon::parse($endStr);

            $baseEvents = CalendarEvent::where('start', '<=', $windowEnd)->get();
            $expandedEvents = collect();

            foreach ($baseEvents as $event) {
                $instances = $this->generateOccurrences($event, $windowStart, $windowEnd);
                foreach ($instances as $instance) {
                    $clonedEvent = clone $event;
                    $clonedEvent->start = $instance['start'];
                    $clonedEvent->end = $instance['end'];
                    $expandedEvents->push($clonedEvent);
                }
            }

            return CalendarEventResource::collection($expandedEvents);
        }

        return CalendarEventResource::collection(CalendarEvent::orderBy('start', 'asc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('create', CalendarEvent::class);

        $validOrganizers = CalendarEvent::getValidOrganizerNames();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'organizer' => ['required', 'string', 'max:255', Rule::in($validOrganizers)],
            'location' => 'nullable|string|max:255',
            'recurrence' => 'nullable|array',
            'recurrence.*' => 'string',
            'is_featured' => 'nullable|boolean',
            'is_hero' => 'nullable|boolean',
        ]);

        $validated['user_id'] = auth()->id() ?? $request->input('user_id');
        $validated['color'] = $validated['color'] ?? '#00698f';
        $validated['recurrence'] = $validated['recurrence'] ?? ['once'];
        $validated['is_featured'] = $validated['is_featured'] ?? false;
        $validated['is_hero'] = $this->canManageHero(auth()->user()) ? (bool) ($validated['is_hero'] ?? false) : false;

        $item = CalendarEvent::create($validated);
        return (new CalendarEventResource($item))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarEvent $calendarEvent)
    {
        return new CalendarEventResource($calendarEvent);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $calendarEvent);

        $validOrganizers = CalendarEvent::getValidOrganizerNames();

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'start' => 'sometimes|required|date',
            'end' => 'sometimes|required|date|after_or_equal:start',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'organizer' => ['sometimes', 'required', 'string', 'max:255', Rule::in($validOrganizers)],
            'location' => 'nullable|string|max:255',
            'recurrence' => 'nullable|array',
            'recurrence.*' => 'string',
            'is_featured' => 'nullable|boolean',
            'is_hero' => 'nullable|boolean',
        ]);

        if (array_key_exists('is_hero', $validated) && !$this->canManageHero(auth()->user())) {
            unset($validated['is_hero']);
        }

        $calendarEvent->update($validated);
        return new CalendarEventResource($calendarEvent->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarEvent $calendarEvent)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', $calendarEvent);

        $calendarEvent->delete();
        return response()->noContent();
    }

    protected function canManageHero(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasRole('super admin')
            || $user->hasRole('rsc')
            || $user->can('manage calendar events');
    }
}
