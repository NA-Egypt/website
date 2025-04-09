<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Option;
use App\Models\Topic;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $meetings = Meeting::all();

        return view('meeting.index', ['meetings' => $meetings]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $topics = Topic::all();

        $groups = Group::all();

        $days = Day::all();

        $options = Option::all();


        return view('meeting.create', [

            'topics'    => $topics,
            'groups'    => $groups,
            'days'      => $days,
            'options'   => $options
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $fields = request()->validate([
            'group_id'      => 'required',
            'topic_id'      => 'required',
            'day_id'        => 'required',
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            'description'   => 'nullable|string|not_regex:/https?:\/\/[^\s]+/',
            'type'          => 'required|in:open,close',
            'options'       => 'nullable|array',
            'options.*'     => 'exists:options,id',
        ]);


        $meeting = Meeting::create([
            'group_id'    => $fields['group_id'],
            'topic_id'    => $fields['topic_id'],
            'day_id'      => $fields['day_id'],
            'start_time'  => $fields['start_time'],
            'end_time'    => $fields['end_time'],
            'description' => $fields['description'],
            'type'        => $fields['type'],
        ]);

        if (!empty($fields['options'])) {
            $meeting->options()->sync($fields['options']);
        } else {
            $meeting->options()->detach();
        }

        return redirect()->route('meeting.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meeting $meeting)
    {
        $topics = Topic::all();

        $groups = Group::all();

        $days = Day::all();

        $options = Option::all();

        return view('meeting.edit', [

            'topics'    => $topics,
            'groups'    => $groups,
            'days'      => $days,
            'meeting'   => $meeting,
            'options'   => $options
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meeting $meeting)
    {
        $fields = $request->validate([
            'group_id'      => 'required|exists:groups,id',
            'topic_id'      => 'required|exists:topics,id',
            'day_id'        => 'required|exists:days,id',
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            'description'   => 'nullable|string',
            'type'          => 'required|in:open,close',
            'options'       => 'nullable|array',
            'options.*'     => 'exists:options,id',
        ]);
    
        $meeting->update([
            'group_id'    => $fields['group_id'],
            'topic_id'    => $fields['topic_id'],
            'day_id'      => $fields['day_id'],
            'start_time'  => $fields['start_time'],
            'end_time'    => $fields['end_time'],
            'description' => $fields['description'],
            'type'        => $fields['type'],
        ]);
    
        $meeting->options()->sync($fields['options'] ?? []);

        return redirect()->route('meeting.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return redirect()->route('meeting.index');
    }
}
