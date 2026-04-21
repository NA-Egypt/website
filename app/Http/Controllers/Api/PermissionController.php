<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PermissionResource::collection(Permission::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $item = Permission::create($request->all());
        return new PermissionResource($item);
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        return new PermissionResource($permission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $permission->update($request->all());
        return new PermissionResource($permission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(null, 204);
    }
}
