<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MeetingResource;
use App\Models\Group;
use App\Models\Meeting;
use App\Services\MeetingFilterService;
use App\Traits\PaginatesApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MeetingController extends Controller
{
    use PaginatesApi;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, MeetingFilterService $filterService)
    {
        $perPageInt = $this->resolvePerPage($request);

        if ($request->hasAny(['day', 'city', 'serviceBody', 'group', 'neighborhood', 'type', 'search', 'virtualOnly', 'englishOnly'])) {
            $meetings = $filterService->filterMeetings($request->all(), $perPageInt);
        } else {
            $query = Meeting::with(['group.neighborhood.city', 'directOnlineGroup', 'day', 'topics', 'options'])
                ->where('status', 'available');
            $meetings = $query->paginate($perPageInt);
        }

        return MeetingResource::collection($meetings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Meeting::class);

        $fields = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'topics' => 'nullable|array|max:3',
            'topics.*' => 'exists:topics,id',
            'day_id' => 'required|exists:days,id',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'notes' => 'nullable|string',
            'type' => 'required',
            'lang' => 'required|in:arabic,english',
            'status' => 'required|in:suspended,available',
            'options' => 'nullable|array',
            'options.*' => 'exists:options,id',
        ]);

        if (isset($fields['group_id']) && ! $request->user()->hasRole('super admin')) {
            $group = Group::findOrFail($fields['group_id']);
            if ($group->user_id !== $request->user()->id) {
                abort(403, 'Unauthorized');
            }
        }

        $options = $fields['options'] ?? null;
        $topics = empty($fields['topics']) ? [6] : $fields['topics'];
        unset($fields['options'], $fields['topics']);

        $fields['topic_id'] = $topics[0];

        $item = Meeting::create($fields);

        if (! empty($options)) {
            $item->options()->sync($options);
        }

        $item->topics()->sync($topics);

        return (new MeetingResource($item->load(['group', 'day', 'topics', 'options'])))->response()->setStatusCode(201);
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
        Gate::authorize('update', $meeting);

        $fields = $request->validate([
            'group_id' => 'sometimes|required|exists:groups,id',
            'topics' => 'nullable|array|max:3',
            'topics.*' => 'exists:topics,id',
            'day_id' => 'sometimes|required|exists:days,id',
            'start_time' => 'sometimes|required',
            'end_time' => 'sometimes|required|after:start_time',
            'notes' => 'nullable|string',
            'type' => 'sometimes|required',
            'lang' => 'sometimes|required|in:arabic,english',
            'status' => 'sometimes|required|in:suspended,available',
            'options' => 'nullable|array',
            'options.*' => 'exists:options,id',
        ]);

        if ($request->has('topics')) {
            $topics = empty($fields['topics']) ? [6] : $fields['topics'];
            $fields['topic_id'] = $topics[0];
            $meeting->topics()->sync($topics);
        }

        if (isset($fields['group_id']) && ! $request->user()->hasRole('super admin')) {
            $group = Group::findOrFail($fields['group_id']);
            if ($group->user_id !== $request->user()->id) {
                abort(403, 'Unauthorized');
            }
        }

        $options = $fields['options'] ?? null;
        unset($fields['options'], $fields['topics']);

        $meeting->update($fields);

        if ($request->has('options')) {
            $meeting->options()->sync($options ?? []);
        }

        return new MeetingResource($meeting->load(['group', 'day', 'topics', 'options']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meeting $meeting)
    {
        Gate::authorize('delete', $meeting);

        $meeting->delete();

        return response()->json(null, 204);
    }
}
