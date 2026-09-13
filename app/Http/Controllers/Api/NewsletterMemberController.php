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
        $validated = $request->validate([
            'email'     => 'required|email|max:255',
            'subscribe' => 'nullable|boolean',
        ]);

        $item = NewsletterMember::create($validated);
        return (new NewsletterMemberResource($item))->response()->setStatusCode(201);
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
        $validated = $request->validate([
            'email'     => 'sometimes|required|email|max:255',
            'subscribe' => 'nullable|boolean',
        ]);

        $newsletterMember->update($validated);
        return new NewsletterMemberResource($newsletterMember);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewsletterMember $newsletterMember)
    {
        $newsletterMember->delete();
        return response()->noContent();
    }
}
