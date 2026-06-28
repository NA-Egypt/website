<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $iputs = $request->validate([
            'name' => 'required|unique:permissions',
            'description'=>'nullable'

        ]);
        Permission::create($iputs );
        return redirect()->route('permissions.index')
            ->with('success', __('messages.permission_created'));
    }

    public function edit(Permission $permission)
    {
        return view('permissions.edit', ['permission' => $permission]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $field = request()->validate([
            'name' => 'required',
            'description'=>'nullable'
        ]);

        $permission->update($field);

        return redirect()->route('permissions.index')
            ->with('success', __('messages.permission_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', __('messages.permission_deleted'));
    }
}
