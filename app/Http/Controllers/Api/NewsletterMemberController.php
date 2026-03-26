<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterMember;
use App\Http\Resources\NewsletterMemberResource;
use Illuminate\Http\Request;

class NewsletterMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return NewsletterMemberResource::collection(NewsletterMember::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $item = NewsletterMember::create($request->all());
        return new NewsletterMemberResource($item);
    }

    /**
     * Display the specified resource.
     */
    public function show(NewsletterMember $newsletterMember)
    {
        return new NewsletterMemberResource($newsletterMember);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NewsletterMember $newsletterMember)
    {
        $newsletterMember->update($request->all());
        return new NewsletterMemberResource($newsletterMember);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewsletterMember $newsletterMember)
    {
        $newsletterMember->delete();
        return response()->json(null, 204);
    }
}
