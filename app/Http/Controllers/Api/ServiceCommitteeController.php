<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCommittee;
use App\Http\Resources\ServiceCommitteeResource;
use Illuminate\Http\Request;

class ServiceCommitteeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ServiceCommitteeResource::collection(ServiceCommittee::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $item = ServiceCommittee::create($request->all());
        return new ServiceCommitteeResource($item);
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceCommittee $serviceCommittee)
    {
        return new ServiceCommitteeResource($serviceCommittee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceCommittee $serviceCommittee)
    {
        $serviceCommittee->update($request->all());
        return new ServiceCommitteeResource($serviceCommittee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCommittee $serviceCommittee)
    {
        $serviceCommittee->delete();
        return response()->json(null, 204);
    }
}
