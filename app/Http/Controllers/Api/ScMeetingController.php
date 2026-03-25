<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScMeeting;
use App\Http\Resources\ScMeetingResource;
use Illuminate\Http\Request;

class ScMeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ScMeetingResource::collection(ScMeeting::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $item = ScMeeting::create($request->all());
        return new ScMeetingResource($item);
    }

    /**
     * Display the specified resource.
     */
    public function show(ScMeeting $scMeeting)
    {
        return new ScMeetingResource($scMeeting);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScMeeting $scMeeting)
    {
        $scMeeting->update($request->all());
        return new ScMeetingResource($scMeeting);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScMeeting $scMeeting)
    {
        $scMeeting->delete();
        return response()->json(null, 204);
    }
}
