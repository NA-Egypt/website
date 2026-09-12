<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Neighborhood;
use Illuminate\Http\Request;

use App\Traits\PaginatesDataTables;

class NeighborhoodController extends Controller
{
    use PaginatesDataTables;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = Neighborhood::with('city')->withCount('groups');

            if ($request->filled('city_id')) {
                $query->where('city_id', $request->input('city_id'));
            }

            if ($request->filled('has_groups')) {
                if ($request->input('has_groups') === 'yes') {
                    $query->has('groups');
                } elseif ($request->input('has_groups') === 'no') {
                    $query->doesntHave('groups');
                }
            }

            $neighborhoods = $this->paginateDataTable($query, $request, ['ar_name', 'en_name', 'city.ar_name', 'city.en_name']);

            $locale = app()->getLocale();
            $neighborhoods->getCollection()->transform(function($n) use ($locale) {
                $n->primary_name = $locale === 'ar' ? ($n->ar_name ?: $n->en_name) : ($n->en_name ?: $n->ar_name);
                $n->secondary_name = $locale === 'ar' ? $n->en_name : $n->ar_name;
                $n->city_name = $n->city ? ($locale === 'ar' ? ($n->city->ar_name ?: $n->city->en_name) : ($n->city->en_name ?: $n->city->ar_name)) : 'N/A';
                $n->city_subname = $n->city ? ($locale === 'ar' ? $n->city->en_name : $n->city->ar_name) : '';
                $n->groups_count = $n->groups_count ?? 0;
                $n->coordinates = ($n->latitude && $n->longitude) ? ($n->latitude . ', ' . $n->longitude) : null;
                return $n;
            });

            return response()->json($neighborhoods);
        }

        $kpiStats = [
            'total_neighborhoods' => Neighborhood::count(),
            'total_cities'        => City::count(),
            'total_groups'        => \App\Models\Group::whereNotNull('neighborhood_id')->count(),
        ];

        $cities = City::all(['id', 'ar_name', 'en_name']);

        return view('nieghborhood.index', [
            'neighborhoods' => collect(),
            'kpiStats'      => $kpiStats,
            'cities'        => $cities
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cities = City::all();
        
        return view('nieghborhood.create', ['cities' => $cities]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = request()->validate([
            'ar_name'      => 'required|min:3|regex:/^[\p{Arabic}0-9 ]+$/u',
            'en_name'      => 'required|min:3|regex:/^[A-Za-z0-9 ]+$/u',
            'city_id'      => 'required',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180'
        ], [
            'ar_name.regex'     => __('messages.The Arabic name must contain only Arabic letters.'),
            'en_name.regex'     => __('messages.The English name must contain only English letters.'),
            'ar_name.required'  => __('messages.This field is required'),
            'en_name.required'  => __('messages.This field is required'),
            'ar_name.min'       => __('messages.You must insert 3 characters at least'),
            'en_name.min'       => __('messages.You must insert 3 characters at least'),
            'city_id.required'  => __('messages.This field is required')
        ]);

        Neighborhood::create($fields);

        return redirect()->route('neighborhood.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Neighborhood $neighborhood)
    {
        $cities = City::all();

        return view('nieghborhood.edit', ['neighborhood'=>$neighborhood, 'cities'=>$cities]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Neighborhood $neighborhood)
    {
        $fields = request()->validate([
            'ar_name'      => 'required|min:3|regex:/^[\p{Arabic}0-9 ]+$/u',
            'en_name'      => 'required|min:3|regex:/^[A-Za-z0-9 ]+$/u',
            'city_id'      => 'required',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180'
        ], [
            'ar_name.regex'     => __('messages.The Arabic name must contain only Arabic letters.'),
            'en_name.regex'     => __('messages.The English name must contain only English letters.'),
            'ar_name.required'  => __('messages.This field is required'),
            'en_name.required'  => __('messages.This field is required'),
            'ar_name.min'       => __('messages.You must insert 3 characters at least'),
            'en_name.min'       => __('messages.You must insert 3 characters at least'),
            'city_id.required'  => __('messages.This field is required')
        ]);

        $neighborhood->update($fields);

        return redirect()->route('neighborhood.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Neighborhood $neighborhood, Request $request)
    {
        $neighborhood->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('messages.neighborhood_deleted_success')]);
        }

        return redirect()->route('neighborhood.index')->with('success', __('messages.neighborhood_deleted_success'));
    }
}
