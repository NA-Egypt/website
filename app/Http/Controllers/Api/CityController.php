<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Cache;

class CityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:super admin', only: ['store', 'update', 'destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = Cache::remember('api_cities_all', 86400, function () {
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
            'ar_name' => 'sometimes|required|string|max:255',
            'en_name' => 'sometimes|required|string|max:255',
            'name' => 'sometimes|required|string|max:255',
            'latitude' => 'nullable|numeric',
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
        Cache::forget('api_cities_all');

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
            'ar_name' => 'sometimes|required|string|max:255',
            'en_name' => 'sometimes|required|string|max:255',
            'name' => 'sometimes|required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if (isset($validated['name'])) {
            $validated['ar_name'] = $validated['ar_name'] ?? $validated['name'];
            $validated['en_name'] = $validated['en_name'] ?? $validated['name'];
            unset($validated['name']);
        }

        $city->update($validated);
        Cache::forget('api_cities_all');

        return new CityResource($city);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();
        Cache::forget('api_cities_all');

        return response()->json(null, 204);
    }
}
