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

        $group_id = null;

        // If user is not super admin, get their assigned group
        if (auth()->user()->cannot('is-super-admin')) {
            $group = Group::whereHas('user', function ($q) {
                $q->where('email', auth()->user()->email);
            })->first();

            if ($group) {
                $group_id = $group->id;
            }
        }

        return view('meeting.create', [
            'topics'    => $topics,
            'groups'    => $groups,
            'days'      => $days,
            'options'   => $options,
            'group_id'  => $group_id
        ]);
    }

//    public function create()
//    {
//        $topics = Topic::all();
//
//        $groups = Group::all();
//
//        $days = Day::all();
//
//        $options = Option::all();
//
//
//        return view('meeting.create', [
//
//            'topics'    => $topics,
//            'groups'    => $groups,
//            'days'      => $days,
//            'options'   => $options
//        ]);
//    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $fields = request()->validate([
            'group_id'      => 'required',
            'topics'        => 'required|array|min:1|max:3',
            'topics.*'      => 'exists:topics,id',
            'day_id'        => 'required',
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            'notes'         => 'nullable|string|not_regex:/https?:\/\/[^\s]+/',
            'type'          => 'required',
            'lang'          => 'required|in:arabic,english',
            'status'        => 'required|in:suspended,available',
            'capacity'      => 'nullable|integer',
            'options'       => 'nullable|array',
            'options.*'     => 'exists:options,id',
        ]);


        $meeting = Meeting::create([
            'group_id'      => $fields['group_id'],
            'day_id'        => $fields['day_id'],
            'start_time'    => $fields['start_time'],
            'end_time'      => $fields['end_time'],
            'notes'         => $fields['notes'],
            'type'          => $fields['type'],
            'lang'          =>$fields['lang'],
            'status'        =>$fields['status'],
            'capacity'      =>$fields['capacity'],
        ]);

        if (!empty($fields['options'])) {
            $meeting->options()->sync($fields['options']);
        } else {
            $meeting->options()->detach();
        }

        if (!empty($fields['topics'])) {
            $meeting->topics()->sync($fields['topics']);
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
            'topics'        => 'required|array|min:1|max:3',
            'topics.*'      => 'exists:topics,id',
            'day_id'        => 'required|exists:days,id',
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            'notes'         => 'nullable|string',
            'type'          => 'required',
            'lang'          => 'required|in:arabic,english',
            'status'        => 'required|in:suspended,available',
            'capacity'      => 'nullable|integer',
            'options'       => 'nullable|array',
            'options.*'     => 'exists:options,id',
        ]);
    
        $meeting->update([
            'group_id'    => $fields['group_id'],
            'day_id'      => $fields['day_id'],
            'start_time'  => $fields['start_time'],
            'end_time'    => $fields['end_time'],
            'notes'       => $fields['notes'],
            'type'        => $fields['type'],
            'lang'          =>$fields['lang'],
            'status'        =>$fields['status'],
            'capacity'      =>$fields['capacity'],
        ]);
    
        $meeting->options()->sync($fields['options'] ?? []);
        $meeting->topics()->sync($fields['topics'] ?? []);

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
