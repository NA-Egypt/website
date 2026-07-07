<?php

namespace App\Http\Controllers;

use App\Http\Requests\DirectOnlineGroupsRequest;
use App\Models\DirectOnlineGroup;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DirectOnlineGroupController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:super admin|rsc', only: ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $directOnlineGroups = DirectOnlineGroup::all();
        return view('direct-online-group.index', ['directOnlineGroups' => $directOnlineGroups]);
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

    public function destroy(DirectOnlineGroup $directOnlineGroup)
    {
        $directOnlineGroup->delete();
        return redirect()->route('direct-online-group.index')->with('success', 'Group deleted successfully.');
    }
}
