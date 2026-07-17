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
            $query = City::query();
            $cities = $this->paginateDataTable($query, $request, ['ar_name', 'en_name']);
            return response()->json($cities);
        }

        $columns = [
            ['field' => 'ar_name', 'title' => __('messages.City Arabic Name'), 'sort' => true],
            ['field' => 'en_name', 'title' => __('messages.City English Name'), 'sort' => true],
            ['field' => 'actions', 'title' => __('messages.Control'), 'sort' => false]
        ];

        return view('city.index', [
            'columns' => $columns
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

        return redirect()->route('city.index');
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

        return redirect()->route('city.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('city.index');
    }
}
