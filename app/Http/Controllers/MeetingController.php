<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Option;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Meeting::class);
        $meetings = Meeting::all();

        return view('meeting.index', ['meetings' => $meetings]);
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        Gate::authorize('create', Meeting::class);
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
        Gate::authorize('create', Meeting::class);

        $fields = request()->validate([
            'group_id'      => 'required',
            'topics'        => 'nullable|array|max:3',
            'topics.*'      => 'exists:topics,id',
            'day_id'        => 'required',
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            'notes'         => 'nullable|string|not_regex:/https?:\/\/[^\s]+/',
            'type'          => 'required',
            'lang'          => 'required|in:arabic,english',
            'status'        => 'required|in:suspended,available',
            'options'       => 'nullable|array',
            'options.*'     => 'exists:options,id',
            'recurrence'    => 'required|array',
            'recurrence.*'  => 'in:weekly,1st,2nd,3rd,4th,5th,last',
        ]);

        if (in_array('weekly', $fields['recurrence']) && count($fields['recurrence']) > 1) {
            return back()->withErrors(['recurrence' => 'Weekly recurrence cannot be selected with specific weeks.'])->withInput();
        }

        $topics = empty($fields['topics']) ? [6] : $fields['topics'];

        $meeting = Meeting::create([
            'group_id'      => $fields['group_id'],
            'topic_id'      => $topics[0],
            'day_id'        => $fields['day_id'],
            'start_time'    => $fields['start_time'],
            'end_time'      => $fields['end_time'],
            'notes'         => $fields['notes'] ?? null,
            'type'          => $fields['type'],
            'lang'          => $fields['lang'],
            'status'        => $fields['status'],
            'recurrence'    => $fields['recurrence'],
        ]);

        if (!empty($fields['options'])) {
            $meeting->options()->sync($fields['options']);
        } else {
            $meeting->options()->detach();
        }

        $meeting->topics()->sync($topics);

        if (auth()->user()->hasRole('super admin')) {
            return redirect()->route('meeting.index')->with('success', 'Meeting created successfully');
        }
        return redirect()->route('dashboard')->with('success', 'Meeting created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meeting $meeting)
    {
        Gate::authorize('update', $meeting);
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
        Gate::authorize('update', $meeting);
        $fields = $request->validate([
            'group_id'      => 'required|exists:groups,id',
            'topics'        => 'nullable|array|max:3',
            'topics.*'      => 'exists:topics,id',
            'day_id'        => 'required|exists:days,id',
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            'notes'         => 'nullable|string',
            'type'          => 'required',
            'lang'          => 'required|in:arabic,english',
            'status'        => 'required|in:suspended,available',
            'options'       => 'nullable|array',
            'options.*'     => 'exists:options,id',
            'recurrence'    => 'required|array',
            'recurrence.*'  => 'in:weekly,1st,2nd,3rd,4th,5th,last',
        ]);
    
        if (in_array('weekly', $fields['recurrence']) && count($fields['recurrence']) > 1) {
            return back()->withErrors(['recurrence' => 'Weekly recurrence cannot be selected with specific weeks.'])->withInput();
        }

        $topics = empty($fields['topics']) ? [6] : $fields['topics'];

        $meeting->update([
            'group_id'    => $fields['group_id'],
            'topic_id'    => $topics[0],
            'day_id'      => $fields['day_id'],
            'start_time'  => $fields['start_time'],
            'end_time'    => $fields['end_time'],
            'notes'       => $fields['notes'] ?? null,
            'type'        => $fields['type'],
            'lang'        => $fields['lang'],
            'status'      => $fields['status'],
            'recurrence'  => $fields['recurrence'],
        ]);
    
        $meeting->options()->sync($fields['options'] ?? []);
        $meeting->topics()->sync($topics);

        if (auth()->user()->hasRole('super admin')) {
            return redirect()->route('meeting.index')->with('success', 'Meeting updated successfully');
        }
        return redirect()->route('dashboard')->with('success', 'Meeting updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meeting $meeting)
    {
        Gate::authorize('delete', $meeting);
        $meeting->delete();

        if (auth()->user()->hasRole('super admin')) {
            return redirect()->route('meeting.index')->with('success', 'Meeting deleted successfully');
        }
        return redirect()->route('dashboard')->with('success', 'Meeting deleted successfully');
    }
}
