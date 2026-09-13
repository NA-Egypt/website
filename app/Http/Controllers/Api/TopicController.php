<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Http\Resources\TopicResource;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TopicResource::collection(Topic::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ar_name'     => 'required|string|max:255',
            'en_name'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $item = Topic::create($validated);
        return (new TopicResource($item))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic)
    {
        return new TopicResource($topic);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'ar_name'     => 'sometimes|required|string|max:255',
            'en_name'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $topic->update($validated);
        return new TopicResource($topic);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
        $topic->delete();
        return response()->noContent();
    }
}
