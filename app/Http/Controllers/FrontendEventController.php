<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class FrontendEventController extends Controller
{
    public function index()
    {
        $allEvents = CalendarEvent::where('end', '>=', now())
            ->orderBy('start', 'asc')
            ->get();

        $events = $allEvents->groupBy(function($event) {
                return \Carbon\Carbon::parse($event->start)->translatedFormat('F Y');
            });

        $allEventsJSON = $allEvents->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start,
                'end' => $event->end,
                'color' => $event->color ?? '#00698f',
                'description' => $event->description,
                'organizer' => $event->organizer,
                'location' => $event->location,
            ];
        })->toJson();

        return view('frontend.events', compact('events', 'allEventsJSON'));
    }
}
