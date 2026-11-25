<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCommitteeResource;
use App\Models\ServiceCommittee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class ServiceCommitteeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:super admin', only: ['store', 'destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ServiceCommitteeResource::collection(ServiceCommittee::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ar_name' => 'required|string|max:255',
            'en_name' => 'nullable|string|max:255',
            'chairman_name' => 'nullable|string|max:255',
            'chairman_phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'ar_address' => 'nullable|string',
            'en_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:service_committees,id',
        ]);

        if (isset($validated['email']) && is_numeric($validated['email'])) {
            $user = User::find((int) $validated['email']);
            if ($user) {
                $validated['user_id'] = $user->id;
                $validated['email'] = $user->email;
            }
        } elseif (isset($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();
            if ($user) {
                $validated['user_id'] = $user->id;
            }
        }

        $item = ServiceCommittee::create($validated);

        return (new ServiceCommitteeResource($item))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceCommittee $serviceCommittee)
    {
        return new ServiceCommitteeResource($serviceCommittee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceCommittee $serviceCommittee)
    {
        Gate::authorize('update', $serviceCommittee);

        $validated = $request->validate([
            'ar_name' => 'sometimes|required|string|max:255',
            'en_name' => 'nullable|string|max:255',
            'chairman_name' => 'nullable|string|max:255',
            'chairman_phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'ar_address' => 'nullable|string',
            'en_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:service_committees,id',
        ]);

        if (isset($validated['email']) && is_numeric($validated['email'])) {
            $user = User::find((int) $validated['email']);
            if ($user) {
                $validated['user_id'] = $user->id;
                $validated['email'] = $user->email;
            }
        } elseif (isset($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();
            if ($user) {
                $validated['user_id'] = $user->id;
            }
        }

        $serviceCommittee->update($validated);

        return new ServiceCommitteeResource($serviceCommittee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCommittee $serviceCommittee)
    {
        $serviceCommittee->delete();

        return response()->noContent();
    }
}
