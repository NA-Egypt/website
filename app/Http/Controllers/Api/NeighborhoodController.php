<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood;
use App\Http\Resources\NeighborhoodResource;
use Illuminate\Http\Request;

class NeighborhoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return NeighborhoodResource::collection(Neighborhood::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        $item = Neighborhood::create($validated);
        return (new NeighborhoodResource($item))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Neighborhood $neighborhood)
    {
        return new NeighborhoodResource($neighborhood);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Neighborhood $neighborhood)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'city_id' => 'sometimes|required|exists:cities,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        $neighborhood->update($validated);
        return new NeighborhoodResource($neighborhood);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Neighborhood $neighborhood)
    {
        $neighborhood->delete();
        return response()->json(null, 204);
    }
}
