<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Http\Resources\MeetingResource;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return MeetingResource::collection(Meeting::with(['group', 'day', 'topics', 'options'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'group_id'      => 'required|exists:groups,id',
            'topics'        => 'nullable|array|max:3',
            'topics.*'      => 'exists:topics,id',
            'day_id'        => 'required|exists:days,id',
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            'notes'         => 'nullable|string',
            'type'          => 'required',
            'lang'          => 'required|in:arabic,english',
            'status'        => 'required|in:suspended,available',
            'capacity'      => 'nullable|integer',
            'options'       => 'nullable|array',
            'options.*'     => 'exists:options,id',
        ]);

        $topics = empty($fields['topics']) ? [6] : $fields['topics'];
        $fields['topic_id'] = $topics[0];

        $item = Meeting::create($fields);

        if (!empty($fields['options'])) {
            $item->options()->sync($fields['options']);
        }

        $item->topics()->sync($topics);

        return new MeetingResource($item->load(['group', 'day', 'topics', 'options']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Meeting $meeting)
    {
        return new MeetingResource($meeting->load(['group', 'day', 'topics', 'options']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meeting $meeting)
    {
        $fields = $request->validate([
            'group_id'      => 'sometimes|required|exists:groups,id',
            'topics'        => 'nullable|array|max:3',
            'topics.*'      => 'exists:topics,id',
            'day_id'        => 'sometimes|required|exists:days,id',
            'start_time'    => 'sometimes|required',
            'end_time'      => 'sometimes|required|after:start_time',
            'notes'         => 'nullable|string',
            'type'          => 'sometimes|required',
            'lang'          => 'sometimes|required|in:arabic,english',
            'status'        => 'sometimes|required|in:suspended,available',
            'capacity'      => 'nullable|integer',
            'options'       => 'nullable|array',
            'options.*'     => 'exists:options,id',
        ]);

        if ($request->has('topics')) {
            $topics = empty($fields['topics']) ? [6] : $fields['topics'];
            $fields['topic_id'] = $topics[0];
            $meeting->topics()->sync($topics);
        }

        $meeting->update($fields);

        if ($request->has('options')) {
            $meeting->options()->sync($fields['options'] ?? []);
        }

        return new MeetingResource($meeting->load(['group', 'day', 'topics', 'options']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meeting $meeting)
    {
        $meeting->delete();
        return response()->json(null, 204);
    }
}
