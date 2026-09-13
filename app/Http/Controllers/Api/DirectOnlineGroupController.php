<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DirectOnlineGroup;
use App\Http\Resources\DirectOnlineGroupResource;
use Illuminate\Http\Request;

class DirectOnlineGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = min((int) $request->get('per_page', 15), 100);
        $groups = DirectOnlineGroup::with(['user', 'meetings'])->paginate($perPage);

        return DirectOnlineGroupResource::collection($groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ar_name'     => 'required|string|max:255',
            'en_name'     => 'required|string|max:255',
            'ar_gsr_name' => 'nullable|string|max:255',
            'en_gsr_name' => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'location'    => 'required|string|max:500',
            'user_id'     => 'nullable|exists:users,id',
        ]);

        $validated['user_id'] = $validated['user_id'] ?? auth()->id();

        $item = DirectOnlineGroup::create($validated);

        return (new DirectOnlineGroupResource($item))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DirectOnlineGroup $directOnlineGroup)
    {
        return new DirectOnlineGroupResource($directOnlineGroup->load(['user', 'meetings']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DirectOnlineGroup $directOnlineGroup)
    {
        $validated = $request->validate([
            'ar_name'     => 'sometimes|required|string|max:255',
            'en_name'     => 'sometimes|required|string|max:255',
            'ar_gsr_name' => 'nullable|string|max:255',
            'en_gsr_name' => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'location'    => 'sometimes|required|string|max:500',
            'user_id'     => 'nullable|exists:users,id',
        ]);

        $directOnlineGroup->update($validated);

        return new DirectOnlineGroupResource($directOnlineGroup->load(['user', 'meetings']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DirectOnlineGroup $directOnlineGroup)
    {
        $directOnlineGroup->delete();

        return response()->noContent();
    }
}
