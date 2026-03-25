<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class FrontendEventController extends Controller
{
    public function index()
    {
        $events = CalendarEvent::where('end', '>=', now())
            ->orderBy('start', 'asc')
            ->get();

        return view('frontend.events', compact('events'));
    }
}
