<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Http\Resources\OptionResource;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return OptionResource::collection(Option::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ar_name' => 'required|string|max:255',
            'en_name' => 'nullable|string|max:255',
        ]);

        $item = Option::create($validated);
        return (new OptionResource($item))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Option $option)
    {
        return new OptionResource($option);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Option $option)
    {
        $validated = $request->validate([
            'ar_name' => 'sometimes|required|string|max:255',
            'en_name' => 'nullable|string|max:255',
        ]);

        $option->update($validated);
        return new OptionResource($option);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Option $option)
    {
        $option->delete();
        return response()->noContent();
    }
}
