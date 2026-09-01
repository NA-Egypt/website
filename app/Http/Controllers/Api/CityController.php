<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Http\Resources\CityResource;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = \Illuminate\Support\Facades\Cache::remember('api_cities_all', 86400, function () {
            return City::all();
        });
        return CityResource::collection($cities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ar_name'   => 'sometimes|required|string|max:255',
            'en_name'   => 'sometimes|required|string|max:255',
            'name'      => 'sometimes|required|string|max:255',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if (isset($validated['name'])) {
            $validated['ar_name'] = $validated['ar_name'] ?? $validated['name'];
            $validated['en_name'] = $validated['en_name'] ?? $validated['name'];
            unset($validated['name']);
        }

        if (empty($validated['ar_name'])) {
            $request->validate(['ar_name' => 'required|string|max:255']);
        }

        $item = City::create($validated);
        \Illuminate\Support\Facades\Cache::forget('api_cities_all');
        return (new CityResource($item))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        return new CityResource($city);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'ar_name'   => 'sometimes|required|string|max:255',
            'en_name'   => 'sometimes|required|string|max:255',
            'name'      => 'sometimes|required|string|max:255',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if (isset($validated['name'])) {
            $validated['ar_name'] = $validated['ar_name'] ?? $validated['name'];
            $validated['en_name'] = $validated['en_name'] ?? $validated['name'];
            unset($validated['name']);
        }

        $city->update($validated);
        \Illuminate\Support\Facades\Cache::forget('api_cities_all');
        return new CityResource($city);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();
        \Illuminate\Support\Facades\Cache::forget('api_cities_all');
        return response()->json(null, 204);
    }
}
