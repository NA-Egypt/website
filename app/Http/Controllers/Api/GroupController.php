<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Http\Resources\GroupResource;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = min((int) $request->get('per_page', 15), 100);
        $relations = ['serviceBody', 'neighborhood'];
        if ($request->user('sanctum') || auth('sanctum')->check() || auth()->check()) {
            $relations[] = 'user';
        }
        $groups = Group::with($relations)->paginate($perPage);
        return GroupResource::collection($groups)
            ->response()
            ->withHeaders(\App\Services\BotDetectionService::noIndexAiHeaders());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ar_name'         => 'required|string|max:255',
            'en_name'         => 'nullable|string|max:255',
            'ar_gsr_name'     => 'nullable|string|max:255',
            'en_gsr_name'     => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:50',
            'location'        => 'nullable|string',
            'ar_address'      => 'nullable|string',
            'en_address'      => 'nullable|string',
            'group_type'      => 'nullable|string|max:50',
            'service_body_id' => 'nullable|exists:service_bodies,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'capacity'        => 'nullable|integer',
            'user_id'         => 'nullable|exists:users,id',
        ]);

        $validated['user_id'] = $validated['user_id'] ?? auth()->id();
        $item = Group::create($validated);
        return (new GroupResource($item))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Group $group)
    {
        $relations = ['serviceBody', 'neighborhood'];
        if ($request->user('sanctum') || auth('sanctum')->check() || auth()->check()) {
            $relations[] = 'user';
        }
        return (new GroupResource($group->load($relations)))
            ->response()
            ->withHeaders(\App\Services\BotDetectionService::noIndexAiHeaders());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'ar_name'         => 'sometimes|required|string|max:255',
            'en_name'         => 'nullable|string|max:255',
            'ar_gsr_name'     => 'nullable|string|max:255',
            'en_gsr_name'     => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:50',
            'location'        => 'nullable|string',
            'ar_address'      => 'nullable|string',
            'en_address'      => 'nullable|string',
            'group_type'      => 'nullable|string|max:50',
            'service_body_id' => 'nullable|exists:service_bodies,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'capacity'        => 'nullable|integer',
            'user_id'         => 'nullable|exists:users,id',
        ]);

        $group->update($validated);
        return new GroupResource($group->load(['serviceBody', 'neighborhood', 'user']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $group->delete();
        return response()->noContent();
    }
}
