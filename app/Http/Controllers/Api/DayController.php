<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Day;
use App\Http\Resources\DayResource;
use Illuminate\Http\Request;

class DayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DayResource::collection(Day::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $item = Day::create($request->all());
        return new DayResource($item);
    }

    /**
     * Display the specified resource.
     */
    public function show(Day $day)
    {
        return new DayResource($day);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Day $day)
    {
        $day->update($request->all());
        return new DayResource($day);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Day $day)
    {
        $day->delete();
        return response()->json(null, 204);
    }
}
