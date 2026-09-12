<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

use App\Traits\PaginatesDataTables;

class PermissionController extends Controller
{
    use PaginatesDataTables;

    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = Permission::query()->withCount('roles');
            $search = $request->input('search');

            if ($request->filled('category')) {
                $locale = app()->getLocale();
                $items = trans('permissions.items', [], $locale);
                $catNames = [];
                if (is_array($items)) {
                    foreach ($items as $permName => $permData) {
                        if (isset($permData['category']) && $permData['category'] === $request->category) {
                            $catNames[] = $permName;
                        }
                    }
                }
                $query->whereIn('name', $catNames);
            }

            if (!empty($search)) {
                $matchedNames = [];
                $locale = app()->getLocale();
                $items = trans('permissions.items', [], $locale);

                if (is_array($items)) {
                    foreach ($items as $permName => $permData) {
                        if (
                            (isset($permData['label']) && mb_stripos($permData['label'], $search) !== false) ||
                            (isset($permData['description']) && mb_stripos($permData['description'], $search) !== false)
                        ) {
                            $matchedNames[] = $permName;
                        }
                    }
                }

                $query->where(function ($q) use ($search, $matchedNames) {
                    $q->where('name', 'like', "%{$search}%");
                    if (!empty($matchedNames)) {
                        $q->orWhereIn('name', $matchedNames);
                    }
                });

                $reqWithoutSearch = $request->duplicate();
                $reqWithoutSearch->query->remove('search');
                $permissions = $this->paginateDataTable($query, $reqWithoutSearch);
            } else {
                $permissions = $this->paginateDataTable($query, $request, ['name']);
            }

            return response()->json($permissions);
        }

        $totalPermissions = Permission::count();
        $totalRoles = \Spatie\Permission\Models\Role::count();
        $assignedPermissions = \Illuminate\Support\Facades\DB::table('role_has_permissions')->distinct()->count('permission_id');
        $locale = app()->getLocale();
        $categoriesCatalog = trans('permissions.categories', [], $locale);
        $totalCategories = is_array($categoriesCatalog) ? count($categoriesCatalog) : 0;

        $kpiStats = [
            'total_permissions' => $totalPermissions,
            'total_roles' => $totalRoles,
            'assigned_permissions' => $assignedPermissions,
            'total_categories' => $totalCategories,
        ];

        $categories = [];
        if (is_array($categoriesCatalog)) {
            foreach ($categoriesCatalog as $key => $cat) {
                $categories[] = [
                    'key' => $key,
                    'title' => is_array($cat) ? ($cat['title'] ?? $key) : $cat,
                ];
            }
        }

        $permissions = collect();
        return view('permissions.index', compact('permissions', 'kpiStats', 'categories'));
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
    public function destroy(Request $request, Permission $permission)
    {
        $permission->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.permission_deleted')
            ]);
        }

        return redirect()->route('permissions.index')
            ->with('success', __('messages.permission_deleted'));
    }
}
