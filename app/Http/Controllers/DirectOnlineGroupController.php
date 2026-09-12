<?php

namespace App\Http\Controllers;

use App\Http\Requests\DirectOnlineGroupsRequest;
use App\Models\DirectOnlineGroup;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use App\Traits\PaginatesDataTables;

class DirectOnlineGroupController extends Controller implements HasMiddleware
{
    use PaginatesDataTables;

    public static function middleware(): array
    {
        return [
            new Middleware('role:super admin|rsc', only: ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = DirectOnlineGroup::with(['user', 'meetings.day', 'meetings.topic'])->withCount('meetings');

            if ($request->filled('has_meetings')) {
                if ($request->input('has_meetings') === 'yes') {
                    $query->has('meetings');
                } elseif ($request->input('has_meetings') === 'no') {
                    $query->doesntHave('meetings');
                }
            }

            if ($request->filled('has_link')) {
                if ($request->input('has_link') === 'yes') {
                    $query->whereNotNull('location')->where('location', '!=', '');
                } elseif ($request->input('has_link') === 'no') {
                    $query->where(function($q) {
                        $q->whereNull('location')->orWhere('location', '');
                    });
                }
            }

            $directOnlineGroups = $this->paginateDataTable($query, $request, ['ar_name', 'en_name', 'user.email', 'location', 'ar_gsr_name', 'en_gsr_name']);

            $locale = app()->getLocale();
            $directOnlineGroups->getCollection()->transform(function($g) use ($locale) {
                $g->primary_name = $locale === 'ar' ? ($g->ar_name ?: $g->en_name) : ($g->en_name ?: $g->ar_name);
                $g->secondary_name = $locale === 'ar' ? $g->en_name : $g->ar_name;
                $g->gsr_name = $locale === 'ar' ? ($g->ar_gsr_name ?: $g->en_gsr_name) : ($g->en_gsr_name ?: $g->ar_gsr_name);
                $g->gsr_email = $g->user ? $g->user->email : null;
                $g->location_link = $g->location;
                $g->meetings_summary = $g->meetings ? $g->meetings->map(function($m) use ($locale) {
                    return [
                        'id' => $m->id,
                        'day' => $m->day ? ($locale === 'ar' ? $m->day->ar_name : $m->day->en_name) : 'N/A',
                        'time' => $m->formatted_start_time,
                        'topic' => $m->topic ? ($locale === 'ar' ? ($m->topic->ar_name ?: $m->topic->en_name) : ($m->topic->en_name ?: $m->topic->ar_name)) : 'N/A',
                        'status' => $m->status
                    ];
                }) : [];

                return $g;
            });

            return response()->json($directOnlineGroups);
        }

        $kpiStats = [
            'total_online_groups'   => DirectOnlineGroup::count(),
            'total_online_meetings' => \App\Models\Meeting::whereNotNull('direct_online_group_id')->count(),
            'active_links_count'    => DirectOnlineGroup::whereNotNull('location')->where('location', '!=', '')->count(),
        ];

        return view('direct-online-group.index', [
            'directOnlineGroups' => collect(),
            'kpiStats'           => $kpiStats
        ]);
    }

    public function create()
    {
        return view('direct-online-group.create');
    }

    public function store(DirectOnlineGroupsRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();

        DirectOnlineGroup::create($validatedData);

        return redirect()->route('direct-online-group.index')->with('success', 'Group created successfully.');
    }

    public function edit(DirectOnlineGroup $directOnlineGroup)
    {
        return view('direct-online-group.edit', ['directOnlineGroup' => $directOnlineGroup]);
    }

    public function update(DirectOnlineGroupsRequest $request, DirectOnlineGroup $directOnlineGroup)
    {
        $validatedData = $request->validated();
        $directOnlineGroup->update($validatedData);

        return redirect()->route('direct-online-group.index')->with('success', 'Group updated successfully.');
    }

    public function show(DirectOnlineGroup $directOnlineGroup)
    {
        $meetings = $directOnlineGroup->meetings()
            ->with('day')
            ->orderBy('day_id')
            ->orderBy('start_time')
            ->get();

        return view('direct-online-group.show', [
            'directOnlineGroup' => $directOnlineGroup,
            'meetings' => $meetings
        ]);
    }

    public function destroy(DirectOnlineGroup $directOnlineGroup, Request $request)
    {
        $directOnlineGroup->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('messages.direct_online_group_deleted_success')]);
        }

        return redirect()->route('direct-online-group.index')->with('success', __('messages.direct_online_group_deleted_success'));
    }
}
