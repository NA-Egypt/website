<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScMeetingResource;
use App\Models\ScMeeting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ScMeetingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:super admin', only: ['store', 'update', 'destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ScMeetingResource::collection(ScMeeting::with(['serviceCommittee', 'day'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_committee_id' => 'required|exists:service_committees,id',
            'day_id' => 'required|exists:days,id',
            'week_number' => 'nullable|integer|min:1|max:5',
            'time' => 'required',
            'notes' => 'nullable|string',
        ]);

        $item = ScMeeting::create($validated);

        return (new ScMeetingResource($item->load(['serviceCommittee', 'day'])))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ScMeeting $scMeeting)
    {
        return new ScMeetingResource($scMeeting->load(['serviceCommittee', 'day']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScMeeting $scMeeting)
    {
        $validated = $request->validate([
            'service_committee_id' => 'sometimes|required|exists:service_committees,id',
            'day_id' => 'sometimes|required|exists:days,id',
            'week_number' => 'nullable|integer|min:1|max:5',
            'time' => 'sometimes|required',
            'notes' => 'nullable|string',
        ]);

        $scMeeting->update($validated);

        return new ScMeetingResource($scMeeting->load(['serviceCommittee', 'day']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScMeeting $scMeeting)
    {
        $scMeeting->delete();

        return response()->noContent();
    }
}
