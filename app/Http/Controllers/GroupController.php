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

class GroupController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:super admin', only: ['create', 'store', 'destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->hasRole('super admin') || $user->hasRole('rsc')) {
            $groups = Group::all();
        } elseif ($user->hasRole('ServiceBody') && $user->service_body_id) {
            $groups = Group::where('service_body_id', $user->service_body_id)->get();
        } elseif ($user->hasRole('gsr')) {
            $groups = Group::where('user_id', $user->id)->get();
        } else {
            $groups = collect();
        }

        return view('group.index', ['groups' => $groups]);
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
    public function destroy(Group $group)
    {
        $group->delete();

        return redirect()->route('group.index');
    }
}
