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
        $validated = $request->validate([
            'name'    => 'nullable|string|max:255',
            'ar_name' => 'nullable|string|max:255',
            'en_name' => 'nullable|string|max:255',
        ]);

        if (empty($validated['name']) && empty($validated['ar_name']) && empty($validated['en_name'])) {
            $request->validate(['name' => 'required|string|max:255']);
        }

        $item = Day::create($validated);
        return (new DayResource($item))->response()->setStatusCode(201);
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
        $validated = $request->validate([
            'name'    => 'nullable|string|max:255',
            'ar_name' => 'nullable|string|max:255',
            'en_name' => 'nullable|string|max:255',
        ]);

        $day->update($validated);
        return new DayResource($day);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Day $day)
    {
        $day->delete();
        return response()->noContent();
    }
}
