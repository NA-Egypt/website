<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityNameRequest;
use App\Models\City;
use Illuminate\Http\Request;

use App\Traits\PaginatesDataTables;

class CityController extends Controller
{
    use PaginatesDataTables;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = City::withCount('neighborhoods')->with('neighborhoods');

            if ($request->filled('has_neighborhoods')) {
                if ($request->input('has_neighborhoods') === 'yes') {
                    $query->has('neighborhoods');
                } elseif ($request->input('has_neighborhoods') === 'no') {
                    $query->doesntHave('neighborhoods');
                }
            }

            $cities = $this->paginateDataTable($query, $request, ['ar_name', 'en_name']);

            $locale = app()->getLocale();
            $cities->getCollection()->transform(function($c) use ($locale) {
                $c->primary_name = $locale === 'ar' ? ($c->ar_name ?: $c->en_name) : ($c->en_name ?: $c->ar_name);
                $c->secondary_name = $locale === 'ar' ? $c->en_name : $c->ar_name;
                $c->neighborhoods_count = $c->neighborhoods_count ?? 0;
                $c->neighborhoods_list = $c->neighborhoods ? $c->neighborhoods->map(function($n) use ($locale) {
                    return $locale === 'ar' ? ($n->ar_name ?: $n->en_name) : ($n->en_name ?: $n->ar_name);
                })->filter()->values()->all() : [];
                return $c;
            });

            return response()->json($cities);
        }

        $kpiStats = [
            'total_cities'        => City::count(),
            'total_neighborhoods' => \App\Models\Neighborhood::count(),
            'total_groups'        => \App\Models\Group::whereNotNull('neighborhood_id')->count(),
        ];

        return view('city.index', [
            'kpiStats' => $kpiStats
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('city.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CityNameRequest $request)
    {
        $validatedData = $request->validated();

        City::create($validatedData);

        return redirect()->route('city.index')->with('success', __('messages.city_created_success'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        return view('city.edit', ['city' => $city]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CityNameRequest $request, City $city)
    {
        $validatedData = $request->validated();

        $city->update($validatedData);

        return redirect()->route('city.index')->with('success', __('messages.city_updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city, Request $request)
    {
        $city->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('messages.city_deleted_success')]);
        }

        return redirect()->route('city.index')->with('success', __('messages.city_deleted_success'));
    }
}
