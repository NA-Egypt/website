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
            $query = Permission::query();
            $search = $request->input('search');

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

        $permissions = collect();
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
