<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceBodyResource;
use App\Http\Resources\ServiceCommitteeResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\ServiceCommittee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $serviceBody = $user->service_body_id ? $user->serviceBody : null;

        $serviceCommittee = null;
        if ($user->hasRole('rsc')) {
            $serviceCommittee = ServiceCommittee::find(83)
                ?: ServiceCommittee::where('email', 'RSC@naegypt.org')->first();
        }
        if (! $serviceCommittee) {
            $serviceCommittee = ServiceCommittee::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();
        }

        $groups = Group::where('user_id', $user->id)
            ->get(['id', 'ar_name', 'en_name', 'service_body_id']);

        return response()->json([
            'user' => new UserResource($user),
            'roles' => $user->getRoleNames()->values(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
            'scope' => [
                'service_body' => $serviceBody ? new ServiceBodyResource($serviceBody) : null,
                'service_committee' => $serviceCommittee ? new ServiceCommitteeResource($serviceCommittee) : null,
                'groups' => $groups,
            ],
        ]);
    }
}
