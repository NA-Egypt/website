<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with(['day', 'servicebody'])->orderBy('date', 'asc')->get();
        return EventResource::collection($events);
    }

    private function authorizeManageEvents(Request $request): void
    {
        $user = $request->user();
        if (!$user || (!$user->can('create calendar events') && !$user->hasRole('Committees') && !$user->hasRole('ServiceBody') && !$user->hasRole('rsc') && !$user->hasRole('super admin'))) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeManageEvents($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'service_body_id' => 'required|exists:service_bodies,id',
            'day_id' => 'required|exists:days,id',
        ]);

        $item = Event::create($validated);
        return (new EventResource($item->load(['day', 'servicebody'])))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return new EventResource($event->load(['day', 'servicebody']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $this->authorizeManageEvents($request);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'date' => 'sometimes|required|date',
            'service_body_id' => 'sometimes|required|exists:service_bodies,id',
            'day_id' => 'sometimes|required|exists:days,id',
        ]);

        $event->update($validated);
        return new EventResource($event->load(['day', 'servicebody']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Event $event)
    {
        $this->authorizeManageEvents($request);

        $event->delete();
        return response()->noContent();
    }
}

