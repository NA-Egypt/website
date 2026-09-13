<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCommittee;
use App\Http\Resources\WorkgroupResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkgroupController extends Controller
{
    /**
     * Resolve the current user's owning parent committee if applicable.
     */
    protected function getMyCommittee(): ?ServiceCommittee
    {
        $user = Auth::user();
        if (!$user) return null;

        if ($user->hasRole('rsc')) {
            $rscCommittee = ServiceCommittee::find(83) ?: ServiceCommittee::where('email', 'RSC@naegypt.org')->first();
            if ($rscCommittee) {
                return $rscCommittee;
            }
        }

        return ServiceCommittee::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();
    }

    /**
     * Display a listing of workgroups.
     */
    public function index(Request $request)
    {
        $query = ServiceCommittee::workgroupsOnly()->with(['parent', 'user', 'meetings']);

        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        return WorkgroupResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created workgroup.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user && $user->hasRole('super admin');
        $myCommittee = $this->getMyCommittee();

        $validated = $request->validate([
            'parent_id'      => 'required|exists:service_committees,id',
            'workgroup_type' => 'required|in:standing,ad_hoc',
            'status'         => 'required|in:active,inactive',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'ar_name'        => 'required|string|max:255',
            'en_name'        => 'required|string|max:255',
            'chairman_name'  => 'nullable|string|max:255',
            'chairman_phone' => 'nullable|string|max:255',
            'email'          => 'nullable|string|max:255',
            'location'       => 'nullable|string',
            'ar_address'     => 'nullable|string',
            'en_address'     => 'nullable|string',
            'notes'          => 'nullable|string',
            'user_id'        => 'nullable|exists:users,id',
        ]);

        // Authorization check: non-superadmins must own the parent committee
        if (!$isSuperAdmin) {
            if (!$myCommittee || $validated['parent_id'] != $myCommittee->id) {
                abort(403, 'Unauthorized to create a workgroup under this committee.');
            }
        }

        $item = ServiceCommittee::create($validated);

        return (new WorkgroupResource($item->load(['parent', 'user', 'meetings'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified workgroup.
     */
    public function show(ServiceCommittee $workgroup)
    {
        if (!$workgroup->isWorkgroup()) {
            abort(404, 'Workgroup not found.');
        }

        return new WorkgroupResource($workgroup->load(['parent', 'user', 'meetings']));
    }

    /**
     * Update the specified workgroup.
     */
    public function update(Request $request, ServiceCommittee $workgroup)
    {
        if (!$workgroup->isWorkgroup()) {
            abort(404, 'Workgroup not found.');
        }

        $user = Auth::user();
        $isSuperAdmin = $user && $user->hasRole('super admin');
        $myCommittee = $this->getMyCommittee();

        // Workgroup user can only update their assigned workgroup
        if ($user && $user->hasRole('Workgroups') && !$isSuperAdmin && !$user->hasRole('Committees') && !$user->hasRole('rsc')) {
            if ($workgroup->user_id !== $user->id && $workgroup->email !== $user->email) {
                abort(403, 'Unauthorized to update this workgroup.');
            }
        } elseif (!$isSuperAdmin) {
            if (!$myCommittee || $workgroup->parent_id != $myCommittee->id) {
                abort(403, 'Unauthorized to manage this workgroup.');
            }
        }

        $validated = $request->validate([
            'parent_id'      => 'sometimes|required|exists:service_committees,id',
            'workgroup_type' => 'sometimes|required|in:standing,ad_hoc',
            'status'         => 'sometimes|required|in:active,inactive',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'ar_name'        => 'sometimes|required|string|max:255',
            'en_name'        => 'sometimes|required|string|max:255',
            'chairman_name'  => 'nullable|string|max:255',
            'chairman_phone' => 'nullable|string|max:255',
            'email'          => 'nullable|string|max:255',
            'location'       => 'nullable|string',
            'ar_address'     => 'nullable|string',
            'en_address'     => 'nullable|string',
            'notes'          => 'nullable|string',
            'user_id'        => 'nullable|exists:users,id',
        ]);

        $workgroup->update($validated);

        return new WorkgroupResource($workgroup->load(['parent', 'user', 'meetings']));
    }

    /**
     * Remove the specified workgroup.
     */
    public function destroy(ServiceCommittee $workgroup)
    {
        if (!$workgroup->isWorkgroup()) {
            abort(404, 'Workgroup not found.');
        }

        $user = Auth::user();
        $isSuperAdmin = $user && $user->hasRole('super admin');
        $myCommittee = $this->getMyCommittee();

        if (!$isSuperAdmin) {
            if (!$myCommittee || $workgroup->parent_id != $myCommittee->id) {
                abort(403, 'Unauthorized to delete this workgroup.');
            }
        }

        $workgroup->delete();

        return response()->noContent();
    }
}
