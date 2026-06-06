<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

use App\Traits\EventRecurrenceTrait;

class FrontendEventController extends Controller
{
    use EventRecurrenceTrait;

    public function index()
    {
        $windowStart = now();
        $windowEnd = now()->addYears(1);

        $baseEvents = CalendarEvent::where('start', '<=', $windowEnd)->get();
        
        $expandedEvents = collect();

        foreach ($baseEvents as $event) {
            $instances = $this->generateOccurrences($event, $windowStart, $windowEnd);
            foreach ($instances as $instance) {
                // Clone the event for display
                $clonedEvent = clone $event;
                $clonedEvent->start = $instance['start'];
                $clonedEvent->end = $instance['end'];
                $expandedEvents->push($clonedEvent);
            }
        }

        // Sort by featured status first (featured events on top), then by start date
        $expandedEvents = $expandedEvents->sort(function($a, $b) {
            $aFeatured = $a->is_featured ? 1 : 0;
            $bFeatured = $b->is_featured ? 1 : 0;
            if ($aFeatured !== $bFeatured) {
                return $bFeatured <=> $aFeatured;
            }
            return $a->start <=> $b->start;
        })->values();

        $events = $expandedEvents->groupBy(function($event) {
                return \Carbon\Carbon::parse($event->start)->translatedFormat('F Y');
            });

        $allEventsJSON = $expandedEvents->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start->toIso8601String(),
                'end' => $event->end->toIso8601String(),
                'color' => $event->color ?? '#00698f',
                'description' => $event->description,
                'organizer' => $event->organizer,
                'location' => $event->location,
                'recurrence' => $event->formatted_recurrence,
            ];
        })->toJson();

        return view('frontend.events', compact('events', 'allEventsJSON'));
    }
}
