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
            $query = Meeting::with(['group.serviceBody', 'group.neighborhood', 'directOnlineGroup', 'topic', 'day', 'options']);

            if ($request->filled('day_id')) {
                $query->where('day_id', $request->input('day_id'));
            }

            if ($request->filled('type')) {
                if ($request->input('type') === 'in_person') {
                    $query->whereNotNull('group_id');
                } elseif ($request->input('type') === 'online') {
                    $query->whereNotNull('direct_online_group_id');
                }
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            $meetings = $this->paginateDataTable($query, $request, [
                'group.en_name', 'group.ar_name', 
                'directOnlineGroup.en_name', 'directOnlineGroup.ar_name', 
                'day.ar_name', 'day.en_name', 'topic.ar_name', 'topic.en_name'
            ]);

            $locale = app()->getLocale();
            $meetings->getCollection()->transform(function($m) use ($locale) {
                $m->group_name = $m->groupOrDirect ? ($locale === 'ar' ? ($m->groupOrDirect->ar_name ?: $m->groupOrDirect->en_name) : ($m->groupOrDirect->en_name ?: $m->groupOrDirect->ar_name)) : 'N/A';
                $m->group_subname = $m->groupOrDirect ? ($locale === 'ar' ? $m->groupOrDirect->en_name : $m->groupOrDirect->ar_name) : '';
                $m->group_type = $m->direct_online_group_id ? 'online' : 'in_person';
                $m->topic_name = $m->topic ? ($locale === 'ar' ? ($m->topic->ar_name ?: $m->topic->en_name) : ($m->topic->en_name ?: $m->topic->ar_name)) : 'N/A';
                
                $dayStr = $locale === 'ar' ? $m->day->ar_name : $m->day->en_name;
                if (!empty($m->recurrence) && !in_array('weekly', $m->recurrence)) {
                    $m->day_name = $m->formatted_recurrence . ' - ' . $dayStr;
                } else {
                    $m->day_name = $dayStr;
                }

                $m->from_time = $m->formatted_start_time;
                $m->to_time = $m->formatted_end_time;
                $m->duration_label = $m->duration;
                $m->status_label = $locale === 'ar' ? __('messages.' . $m->status) : ucfirst($m->status);
                $m->options_labels = $m->options ? $m->options->pluck($locale . '_name')->filter()->values()->all() : [];
                $m->zoom_link = $m->directOnlineGroup ? $m->directOnlineGroup->location : null;
                $m->location_address = $m->group ? ($locale === 'ar' ? ($m->group->ar_address ?: $m->group->en_address) : ($m->group->en_address ?: $m->group->ar_address)) : null;
                $m->map_location = $m->group ? $m->group->location : null;

                return $m;
            });

            return response()->json($meetings);
        }

        $kpiStats = [
            'total_meetings'     => Meeting::count(),
            'in_person_meetings' => Meeting::whereNotNull('group_id')->count(),
            'online_meetings'    => Meeting::whereNotNull('direct_online_group_id')->count(),
            'active_meetings'    => Meeting::where('status', 'available')->count(),
        ];

        $days = Day::all(['id', 'ar_name', 'en_name']);

        return view('meeting.index', [
            'meetings' => collect(),
            'kpiStats' => $kpiStats,
            'days'     => $days
        ]);
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
    public function destroy(Meeting $meeting, Request $request)
    {
        Gate::authorize('delete', $meeting);
        $meeting->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('messages.meeting_deleted_success')]);
        }

        if (auth()->user()->hasRole('super admin')) {
            return redirect()->route('meeting.index')->with('success', __('messages.meeting_deleted_success'));
        }
        return redirect()->route('dashboard')->with('success', __('messages.meeting_deleted_success'));
    }
}
