<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupsRequest;
use App\Models\Group;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use App\Traits\PaginatesDataTables;

class GroupController extends Controller implements HasMiddleware
{
    use PaginatesDataTables;

    public static function middleware(): array
    {
        return [
            new Middleware('role:super admin', only: ['create', 'store', 'destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->hasRole('super admin') || $user->hasRole('rsc')) {
            $query = Group::query();
        } elseif ($user->hasRole('ServiceBody') && $user->service_body_id) {
            $query = Group::where('service_body_id', $user->service_body_id);
        } elseif ($user->hasRole('gsr')) {
            $query = Group::where('user_id', $user->id);
        } else {
            $query = Group::whereRaw('1 = 0');
        }

        if ($request->wantsJson() || $request->ajax()) {
            $query->with(['user', 'serviceBody', 'neighborhood', 'meetings.day', 'meetings.topic'])->withCount('meetings');

            if ($request->filled('service_body_id')) {
                $query->where('service_body_id', $request->input('service_body_id'));
            }

            if ($request->filled('neighborhood_id')) {
                $query->where('neighborhood_id', $request->input('neighborhood_id'));
            }

            if ($request->filled('has_meetings')) {
                if ($request->input('has_meetings') === 'yes') {
                    $query->has('meetings');
                } elseif ($request->input('has_meetings') === 'no') {
                    $query->doesntHave('meetings');
                }
            }

            $groups = $this->paginateDataTable($query, $request, [
                'ar_name', 'en_name', 'user.email', 
                'serviceBody.ar_name', 'serviceBody.en_name', 
                'neighborhood.ar_name', 'neighborhood.en_name'
            ]);

            $locale = app()->getLocale();
            $groups->getCollection()->transform(function($g) use ($locale) {
                $g->primary_name = $locale === 'ar' ? ($g->ar_name ?: $g->en_name) : ($g->en_name ?: $g->ar_name);
                $g->secondary_name = $locale === 'ar' ? $g->en_name : $g->ar_name;
                $g->service_body_name = $g->serviceBody ? ($locale === 'ar' ? ($g->serviceBody->ar_name ?: $g->serviceBody->en_name) : ($g->serviceBody->en_name ?: $g->serviceBody->ar_name)) : 'N/A';
                $g->neighborhood_name = $g->neighborhood ? ($locale === 'ar' ? ($g->neighborhood->ar_name ?: $g->neighborhood->en_name) : ($g->neighborhood->en_name ?: $g->neighborhood->ar_name)) : 'N/A';
                $g->gsr_name = $locale === 'ar' ? ($g->ar_gsr_name ?: $g->en_gsr_name) : ($g->en_gsr_name ?: $g->ar_gsr_name);
                $g->gsr_email = $g->user ? $g->user->email : null;
                $g->address_display = $locale === 'ar' ? ($g->ar_address ?: $g->en_address) : ($g->en_address ?: $g->ar_address);
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

            return response()->json($groups);
        }

        $baseQuery = clone $query;
        $kpiStats = [
            'total_groups'         => (clone $baseQuery)->count(),
            'total_meetings'       => \App\Models\Meeting::whereNotNull('group_id')->count(),
            'service_bodies_count' => \App\Models\ServiceBody::count(),
            'active_gsrs_count'    => (clone $baseQuery)->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
        ];

        $serviceBodies = ServiceBody::all(['id', 'ar_name', 'en_name']);

        return view('group.index', [
            'groups'         => collect(),
            'kpiStats'       => $kpiStats,
            'serviceBodies'  => $serviceBodies
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $serviceBodies = ServiceBody::all();

        $neighborhoods = Neighborhood::all();

        $users = User::all();

        return view('group.create', [
            'serviceBodies' => $serviceBodies,
            'neighborhoods' => $neighborhoods,
            'users'         =>$users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupsRequest $request)
    {


        $validatedData = $request->validated();

        Group::create($validatedData);

        return redirect()->route('group.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        Gate::authorize('update', $group);
        $serviceBodies = ServiceBody::all();

        $neighborhoods = Neighborhood::all();

        $users = User::all();

        return view('group.edit', [
            'group'         => $group,
            'serviceBodies' => $serviceBodies,
            'neighborhoods' => $neighborhoods,
            'users'         =>$users
        ]);
    }

    public function update(GroupsRequest $request, Group $group)
    {
        Gate::authorize('update', $group);
        $validatedData = $request->validated();

        $group->update($validatedData);

        if (auth()->user()->hasRole('super admin')) {
            return redirect()->route('group.index')->with('success', __('messages.group_updated_success'));
        }

        return redirect()->route('group.show', $group->id)->with('success', __('messages.group_updated_success'));
    }

    public function show(Group $group)
    {
        Gate::authorize('view', $group);
//        return view('group.show', ['group'=>$group]);
        // Eager load meetings with days relationship and ordering
        $group->load(['meetings' => function($query) {
            $query->with('day', 'options') // Load the day relationship
            ->orderBy('day_id') // Order by day_id
            ->orderBy('start_time');
        }]);

        return view('group.show', [
            'group' => $group,
            'meetings' => $group->meetings
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group, Request $request)
    {
        $group->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('messages.group_deleted_success')]);
        }

        return redirect()->route('group.index')->with('success', __('messages.group_deleted_success'));
    }
}
