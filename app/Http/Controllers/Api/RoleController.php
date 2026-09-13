<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return RoleResource::collection(Role::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'guard_name' => 'nullable|string|max:255',
        ]);

        $validated['guard_name'] = $validated['guard_name'] ?? 'web';

        $item = Role::create($validated);
        return (new RoleResource($item))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'       => 'sometimes|required|string|max:255',
            'guard_name' => 'nullable|string|max:255',
        ]);

        $role->update($validated);
        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->noContent();
    }
}
