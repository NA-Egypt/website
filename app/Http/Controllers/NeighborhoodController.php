<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Neighborhood;
use Illuminate\Http\Request;

class NeighborhoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $neighborhoods = Neighborhood::all();

        return view('nieghborhood.index', ['neighborhoods' => $neighborhoods]);
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
            'city_id'      => 'required'
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
            'city_id'      => 'required'
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
    public function destroy(Neighborhood $neighborhood)
    {
        $neighborhood->delete();

        return redirect()->route('neighborhood.index');
    }
}
