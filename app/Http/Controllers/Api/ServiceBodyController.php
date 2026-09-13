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
        return ServiceBodyResource::collection(ServiceBody::with('day')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ar_name'     => 'required|string|max:255',
            'en_name'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'day_id'      => 'nullable|exists:days,id',
            'date'        => 'nullable|date',
            'start_time'  => 'nullable',
            'end_time'    => 'nullable',
            'location'    => 'nullable|string',
            'recurrence'  => 'nullable|array',
        ]);

        $item = ServiceBody::create($validated);
        return (new ServiceBodyResource($item->load('day')))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceBody $serviceBody)
    {
        return new ServiceBodyResource($serviceBody->load('day'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceBody $serviceBody)
    {
        $validated = $request->validate([
            'ar_name'     => 'sometimes|required|string|max:255',
            'en_name'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'day_id'      => 'nullable|exists:days,id',
            'date'        => 'nullable|date',
            'start_time'  => 'nullable',
            'end_time'    => 'nullable',
            'location'    => 'nullable|string',
            'recurrence'  => 'nullable|array',
        ]);

        $serviceBody->update($validated);
        return new ServiceBodyResource($serviceBody->load('day'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceBody $serviceBody)
    {
        $serviceBody->delete();
        return response()->noContent();
    }
}
