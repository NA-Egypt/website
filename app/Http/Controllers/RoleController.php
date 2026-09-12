<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\PaginatesDataTables;

class RoleController extends Controller
{
    use PaginatesDataTables;

    /**
     * Core system roles that must be protected against deletion and renaming.
     */
    public const SYSTEM_ROLES = ['super admin', 'rsc', 'Committees', 'Workgroups', 'gsr'];

    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = Role::with('permissions')->withCount(['users', 'permissions']);
            $roles = $this->paginateDataTable($query, $request, ['name', 'description', 'permissions.name']);

            $roles->getCollection()->transform(function ($role) {
                $categoryCounts = [
                    'agenda' => 0,
                    'store' => 0,
                    'calendar' => 0,
                    'forms' => 0,
                    'general' => 0,
                ];

                foreach ($role->permissions as $permission) {
                    $cat = $permission->category ?? 'general';
                    if (isset($categoryCounts[$cat])) {
                        $categoryCounts[$cat]++;
                    } else {
                        $categoryCounts['general']++;
                    }
                }

                $role->category_counts = $categoryCounts;
                $role->is_system_role = in_array(strtolower($role->name), array_map('strtolower', self::SYSTEM_ROLES));
                return $role;
            });

            return response()->json($roles);
        }

        $roles = collect();
        $kpiStats = [
            'total_roles' => Role::count(),
            'system_roles' => Role::whereIn('name', self::SYSTEM_ROLES)->count(),
            'total_permissions' => Permission::count(),
            'total_assignments' => DB::table('model_has_roles')->count(),
        ];

        return view('roles.index', compact('roles', 'kpiStats'));
    }

    public function details(Role $role)
    {
        $role->load(['permissions', 'users:id,name,email']);
        $role->is_system_role = in_array(strtolower($role->name), array_map('strtolower', self::SYSTEM_ROLES));

        $permissions = Permission::whereIn('id', $role->permissions->pluck('id'))->get();
        $groupedPermissions = Permission::getGrouped($permissions);

        return response()->json([
            'role' => $role,
            'grouped_permissions' => $groupedPermissions,
            'users' => $role->users,
        ]);
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

        return redirect()->route('roles.index')->with('success', __('messages.role_created_success'));
    }

    public function update(Request $request, Role $role)
    {
        $isSystemRole = in_array(strtolower($role->name), array_map('strtolower', self::SYSTEM_ROLES));

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:1000',
        ]);

        if ($isSystemRole && strtolower($role->name) !== strtolower($validated['name'])) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.system_role_cannot_be_renamed'),
                ], 422);
            }
            return back()->with('error', __('messages.system_role_cannot_be_renamed'));
        }

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.role_updated_success'),
                'role' => $role,
            ]);
        }

        return redirect()->route('roles.index')->with('success', __('messages.role_updated_success'));
    }

    public function assignPermissions(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.assign-permissions', compact('role', 'permissions'));
    }

    // Update permissions for a role
    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate(['permissions' => 'required|array']);
        $role->permissions()->sync($request->permissions);
        return redirect()->route('roles.index')
            ->with('success', __('messages.permissions_updated'));
    }

    public function destroy(Request $request, Role $role)
    {
        if (in_array(strtolower($role->name), array_map('strtolower', self::SYSTEM_ROLES))) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.system_role_cannot_be_deleted'),
                ], 403);
            }
            return back()->with('error', __('messages.system_role_cannot_be_deleted'));
        }

        $role->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.role_deleted'),
            ]);
        }

        return redirect()->route('roles.index')
            ->with('success', __('messages.role_deleted'));
    }
}
