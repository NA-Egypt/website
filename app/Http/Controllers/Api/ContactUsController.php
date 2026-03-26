<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Http\Resources\ContactUsResource;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ContactUsResource::collection(ContactUs::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $item = ContactUs::create($request->all());
        return new ContactUsResource($item);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactUs $contactUs)
    {
        return new ContactUsResource($contactUs);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactUs $contactUs)
    {
        $contactUs->update($request->all());
        return new ContactUsResource($contactUs);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactUs $contactUs)
    {
        $contactUs->delete();
        return response()->json(null, 204);
    }
}
