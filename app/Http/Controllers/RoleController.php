<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->permissions) {
            $role->permissions()->attach($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully');
    }

    public function assignPermissions(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.assign-permissions', compact('role', 'permissions'));
    }

    // Update permissions for a rule
    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate(['permissions' => 'required|array']);
        $role->permissions()->sync($request->permissions);
        return redirect()->route('roles.index')
            ->with('success', 'Permissions updated!');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role Deleted!');
    }
}
