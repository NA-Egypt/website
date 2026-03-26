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
        $item = Neighborhood::create($request->all());
        return new NeighborhoodResource($item);
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
        $neighborhood->update($request->all());
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
