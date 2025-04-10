<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupsRequest;
use App\Models\Group;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\User;
use Illuminate\Http\Request;


class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::all();

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
    public function store(Request $request)
    {
        dd($request->all());

        $validatedData = $request->validated();

        Group::create($validatedData);

        return redirect()->route('group.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        $serviceBodies = ServiceBody::all();

        $neighborhoods = Neighborhood::all();

        return view('group.edit', [
            'group'         => $group,
            'serviceBodies' => $serviceBodies,
            'neighborhoods' => $neighborhoods,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GroupsRequest $request, Group $group)
    {
        $validatedData = $request->validated();

        $group->update($validatedData);

        return redirect()->route('group.index');
    }

    public function show(Group $group)
    {
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
