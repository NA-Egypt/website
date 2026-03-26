<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceBody;
use App\Http\Resources\ServiceBodyResource;
use Illuminate\Http\Request;

class ServiceBodyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ServiceBodyResource::collection(ServiceBody::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $item = ServiceBody::create($request->all());
        return new ServiceBodyResource($item);
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceBody $serviceBody)
    {
        return new ServiceBodyResource($serviceBody);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceBody $serviceBody)
    {
        $serviceBody->update($request->all());
        return new ServiceBodyResource($serviceBody);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceBody $serviceBody)
    {
        $serviceBody->delete();
        return response()->json(null, 204);
    }
}
