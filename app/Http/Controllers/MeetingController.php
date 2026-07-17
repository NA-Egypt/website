<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Group;
use App\Models\DirectOnlineGroup;
use App\Models\Meeting;
use App\Models\Option;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Traits\PaginatesDataTables;

class MeetingController extends Controller
{
    use PaginatesDataTables;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Meeting::class);

        if ($request->wantsJson() || $request->ajax()) {
            $query = Meeting::with(['group', 'directOnlineGroup', 'topic', 'day']);
            $meetings = $this->paginateDataTable($query, $request, [
                'group.en_name', 'group.ar_name', 
                'directOnlineGroup.en_name', 'directOnlineGroup.ar_name', 
                'day.ar_name', 'day.en_name', 'topic.ar_name', 'topic.en_name'
            ]);

            $meetings->getCollection()->transform(function($m) {
                $m->group_name = $m->groupOrDirect ? (app()->getLocale() === 'ar' ? ($m->groupOrDirect->ar_name ?: $m->groupOrDirect->en_name) : ($m->groupOrDirect->en_name ?: $m->groupOrDirect->ar_name)) : 'N/A';
                $m->topic_name = $m->topic ? (app()->getLocale() === 'ar' ? ($m->topic->ar_name ?: $m->topic->en_name) : ($m->topic->en_name ?: $m->topic->ar_name)) : 'N/A';
                
                $dayStr = app()->getLocale() === 'ar' ? $m->day->ar_name : $m->day->en_name;
                if (!empty($m->recurrence) && !in_array('weekly', $m->recurrence)) {
                    $m->day_name = $m->formatted_recurrence . ' - ' . $dayStr;
                } else {
                    $m->day_name = $dayStr;
                }

                $m->from_time = $m->formatted_start_time;
                $m->to_time = $m->formatted_end_time;
                $m->status_label = app()->getLocale() === 'ar' ? __('messages.' . $m->status) : $m->status;
                return $m;
            });

            return response()->json($meetings);
        }

        $meetings = collect();
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
        $directOnlineGroups = DirectOnlineGroup::all();
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
            'directOnlineGroups' => $directOnlineGroups,
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
            'group_id'               => 'required_without:direct_online_group_id|nullable|exists:groups,id',
            'direct_online_group_id' => 'required_without:group_id|nullable|exists:direct_online_groups,id',
            'topics'                 => 'nullable|array|max:3',
            'topics.*'               => 'exists:topics,id',
            'day_id'                 => 'required',
            'start_time'             => 'required',
            'end_time'               => 'required|after:start_time',
            'notes'                  => 'nullable|string|not_regex:/https?:\/\/[^\s]+/',
            'type'                   => 'required',
            'lang'                   => 'required|in:' . ($request->filled('direct_online_group_id') ? 'arabic' : 'arabic,english'),
            'status'                 => 'required|in:suspended,available',
            'options'                => 'nullable|array',
            'options.*'              => 'exists:options,id',
            'recurrence'             => 'required|array',
            'recurrence.*'           => 'in:weekly,1st,2nd,3rd,4th,5th,last',
        ]);

        if (in_array('weekly', $fields['recurrence']) && count($fields['recurrence']) > 1) {
            return back()->withErrors(['recurrence' => 'Weekly recurrence cannot be selected with specific weeks.'])->withInput();
        }

        $topics = empty($fields['topics']) ? [6] : $fields['topics'];

        $businessTopic = \App\Models\Topic::where('en_name', 'Group Business Meeting')->first();
        if ($businessTopic && in_array($businessTopic->id, $topics)) {
            if (count($topics) > 1) {
                return back()->withErrors(['topics' => __('messages.group_business_meeting_exclusive')])->withInput();
            }
            $fields['type'] = 'closed';
        }

        $meeting = Meeting::create([
            'group_id'               => $fields['group_id'] ?? null,
            'direct_online_group_id' => $fields['direct_online_group_id'] ?? null,
            'topic_id'               => $topics[0],
            'day_id'                 => $fields['day_id'],
            'start_time'             => $fields['start_time'],
            'end_time'               => $fields['end_time'],
            'notes'                  => $fields['notes'] ?? null,
            'type'                   => $fields['type'],
            'lang'                   => $fields['lang'],
            'status'                 => $fields['status'],
            'recurrence'             => $fields['recurrence'],
        ]);

        if (!empty($fields['options'])) {
            $meeting->options()->sync($fields['options']);
        } else {
            $meeting->options()->detach();
        }

        $meeting->topics()->sync($topics);

        if (auth()->user()->hasRole('super admin')) {
            return redirect()->route('meeting.index')->with('success', __('messages.meeting_created_success'));
        }
        return redirect()->route('dashboard')->with('success', __('messages.meeting_created_success'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meeting $meeting)
    {
        Gate::authorize('update', $meeting);
        $topics = Topic::all();
        $groups = Group::all();
        $directOnlineGroups = DirectOnlineGroup::all();
        $days = Day::all();
        $options = Option::all();

        return view('meeting.edit', [
            'topics'             => $topics,
            'groups'             => $groups,
            'directOnlineGroups' => $directOnlineGroups,
            'days'               => $days,
            'meeting'            => $meeting,
            'options'            => $options
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meeting $meeting)
    {
        Gate::authorize('update', $meeting);
        $fields = $request->validate([
            'group_id'               => 'required_without:direct_online_group_id|nullable|exists:groups,id',
            'direct_online_group_id' => 'required_without:group_id|nullable|exists:direct_online_groups,id',
            'topics'                 => 'nullable|array|max:3',
            'topics.*'               => 'exists:topics,id',
            'day_id'                 => 'required|exists:days,id',
            'start_time'             => 'required',
            'end_time'               => 'required|after:start_time',
            'notes'                  => 'nullable|string',
            'type'                   => 'required',
            'lang'                   => 'required|in:' . ($request->filled('direct_online_group_id') ? 'arabic' : 'arabic,english'),
            'status'                 => 'required|in:suspended,available',
            'options'                => 'nullable|array',
            'options.*'              => 'exists:options,id',
            'recurrence'             => 'required|array',
            'recurrence.*'           => 'in:weekly,1st,2nd,3rd,4th,5th,last',
        ]);
    
        if (in_array('weekly', $fields['recurrence']) && count($fields['recurrence']) > 1) {
            return back()->withErrors(['recurrence' => 'Weekly recurrence cannot be selected with specific weeks.'])->withInput();
        }

        $topics = empty($fields['topics']) ? [6] : $fields['topics'];

        $businessTopic = \App\Models\Topic::where('en_name', 'Group Business Meeting')->first();
        if ($businessTopic && in_array($businessTopic->id, $topics)) {
            if (count($topics) > 1) {
                return back()->withErrors(['topics' => __('messages.group_business_meeting_exclusive')])->withInput();
            }
            $fields['type'] = 'closed';
        }

        $meeting->update([
            'group_id'               => $fields['group_id'] ?? null,
            'direct_online_group_id' => $fields['direct_online_group_id'] ?? null,
            'topic_id'               => $topics[0],
            'day_id'                 => $fields['day_id'],
            'start_time'             => $fields['start_time'],
            'end_time'               => $fields['end_time'],
            'notes'                  => $fields['notes'] ?? null,
            'type'                   => $fields['type'],
            'lang'                   => $fields['lang'],
            'status'                 => $fields['status'],
            'recurrence'             => $fields['recurrence'],
        ]);
    
        $meeting->options()->sync($fields['options'] ?? []);
        $meeting->topics()->sync($topics);

        if (auth()->user()->hasRole('super admin')) {
            return redirect()->route('meeting.index')->with('success', __('messages.meeting_updated_success'));
        }
        return redirect()->route('dashboard')->with('success', __('messages.meeting_updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meeting $meeting)
    {
        Gate::authorize('delete', $meeting);
        $meeting->delete();

        if (auth()->user()->hasRole('super admin')) {
            return redirect()->route('meeting.index')->with('success', __('messages.meeting_deleted_success'));
        }
        return redirect()->route('dashboard')->with('success', __('messages.meeting_deleted_success'));
    }
}
