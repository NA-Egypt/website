<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCommittee;
use App\Http\Resources\ServiceCommitteeResource;
use Illuminate\Http\Request;

class ServiceCommitteeController extends Controller
{
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
        $fields = $request->all();
        if (isset($fields['email']) && is_numeric($fields['email'])) {
            $user = \App\Models\User::find((int)$fields['email']);
            if ($user) {
                $fields['user_id'] = $user->id;
                $fields['email'] = $user->email;
            }
        } elseif (isset($fields['email'])) {
            $user = \App\Models\User::where('email', $fields['email'])->first();
            if ($user) {
                $fields['user_id'] = $user->id;
            }
        }

        $item = ServiceCommittee::create($fields);
        return new ServiceCommitteeResource($item);
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
        $fields = $request->all();
        if (isset($fields['email']) && is_numeric($fields['email'])) {
            $user = \App\Models\User::find((int)$fields['email']);
            if ($user) {
                $fields['user_id'] = $user->id;
                $fields['email'] = $user->email;
            }
        } elseif (isset($fields['email'])) {
            $user = \App\Models\User::where('email', $fields['email'])->first();
            if ($user) {
                $fields['user_id'] = $user->id;
            }
        }

        $serviceCommittee->update($fields);
        return new ServiceCommitteeResource($serviceCommittee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCommittee $serviceCommittee)
    {
        $serviceCommittee->delete();
        return response()->json(null, 204);
    }
}
