<?php

namespace App\Http\Controllers;

use App\Models\ServiceCommittee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Traits\PaginatesDataTables;

class WorkgroupController extends Controller implements HasMiddleware
{
    use PaginatesDataTables;

    public static function middleware(): array
    {
        return [
            new Middleware('role:super admin|Committees|rsc|Workgroups'),
        ];
    }

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
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super admin');
        $myCommittee = $this->getMyCommittee();

        // If user is strictly a Workgroup user, redirect to their workgroup show/edit
        if (!$isSuperAdmin && !$user->hasRole('Committees') && !$user->hasRole('rsc') && $user->hasRole('Workgroups')) {
            $myWorkgroup = ServiceCommittee::workgroupsOnly()
                ->where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            if ($myWorkgroup) {
                return redirect()->route('workgroup.show', $myWorkgroup->id);
            }
            abort(403, 'No workgroup assigned.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            $query = ServiceCommittee::workgroupsOnly()->with(['parent', 'user']);

            if (!$isSuperAdmin) {
                if (!$myCommittee) {
                    return response()->json([
                        'data' => [],
                        'total' => 0,
                    ]);
                }
                $query->where('parent_id', $myCommittee->id);
            }

            $searchableColumns = ['ar_name', 'en_name', 'email', 'chairman_name', 'notes', 'workgroup_type', 'status'];
            $result = $this->paginateDataTable($query, $request, $searchableColumns);

            // Add formatted parent committee name
            $isRtl = app()->getLocale() === 'ar';
            if (isset($result->items)) {
                foreach ($result->items() as $item) {
                    $item->parent_name = $item->parent ? ($isRtl ? $item->parent->ar_name : $item->parent->en_name) : '-';
                }
            }

            return response()->json($result);
        }

        return view('workgroups.index', [
            'isSuperAdmin' => $isSuperAdmin,
            'myCommittee' => $myCommittee,
        ]);
    }

    /**
     * Show the form for creating a new workgroup.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super admin');
        $myCommittee = $this->getMyCommittee();

        if (!$isSuperAdmin && !$myCommittee) {
            abort(403, 'You must represent a committee to create a workgroup.');
        }

        $committees = $isSuperAdmin ? ServiceCommittee::committeesOnly()->orderBy('ar_name')->get() : collect([$myCommittee]);
        $users = User::orderBy('name')->get();
        $preselectedParentId = $request->query('parent_id') ?? ($myCommittee ? $myCommittee->id : null);

        return view('workgroups.create', compact('committees', 'myCommittee', 'users', 'isSuperAdmin', 'preselectedParentId'));
    }

    /**
     * Store a newly created workgroup in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super admin');
        $myCommittee = $this->getMyCommittee();

        $rules = [
            'ar_name' => 'required|string|max:255',
            'en_name' => 'required|string|max:255',
            'workgroup_type' => 'required|in:permanent,temporary',
            'status' => 'required|in:active,completed,archived',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'chairman_name' => 'nullable|string|max:255',
            'chairman_phone' => 'nullable|string|max:255',
            'email' => 'required',
            'location' => 'nullable|string|max:255',
            'ar_address' => 'nullable|string|max:255',
            'en_address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'default_footer' => 'nullable|string|max:1000',
        ];

        if ($isSuperAdmin) {
            $rules['parent_id'] = 'required|exists:service_committees,id';
        }

        $request->validate($rules);

        $fields = $request->except('logo');

        if (!$isSuperAdmin) {
            if (!$myCommittee) {
                abort(403, 'Unauthorized');
            }
            $fields['parent_id'] = $myCommittee->id;
        }

        // Resolve user assignment
        if (isset($fields['email']) && is_numeric($fields['email'])) {
            $assignedUser = User::find((int)$fields['email']);
            if ($assignedUser) {
                $fields['user_id'] = $assignedUser->id;
                $fields['email'] = $assignedUser->email;
            }
        } elseif (isset($fields['email'])) {
            $assignedUser = User::where('email', $fields['email'])->first();
            if ($assignedUser) {
                $fields['user_id'] = $assignedUser->id;
            }
        }

        if (isset($assignedUser) && $assignedUser) {
            $assignedUser->assignRole('Workgroups');
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $fields['logo'] = $path;
        }

        $workgroup = ServiceCommittee::create($fields);

        return redirect()->route('workgroup.index')->with('success', __('messages.Workgroup created successfully'));
    }

    /**
     * Display the specified workgroup.
     */
    public function show(string $id)
    {
        $workgroup = ServiceCommittee::workgroupsOnly()->with(['parent', 'user', 'meetings'])->findOrFail($id);
        Gate::authorize('view', $workgroup);

        return view('workgroups.show', compact('workgroup'));
    }

    /**
     * Show the form for editing the specified workgroup.
     */
    public function edit(string $id)
    {
        $workgroup = ServiceCommittee::workgroupsOnly()->with(['parent', 'user'])->findOrFail($id);
        Gate::authorize('update', $workgroup);

        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super admin');
        $myCommittee = $this->getMyCommittee();
        $committees = $isSuperAdmin ? ServiceCommittee::committeesOnly()->orderBy('ar_name')->get() : collect([$workgroup->parent]);
        $users = User::orderBy('name')->get();

        return view('workgroups.edit', compact('workgroup', 'committees', 'users', 'isSuperAdmin', 'myCommittee'));
    }

    /**
     * Update the specified workgroup in storage.
     */
    public function update(Request $request, string $id)
    {
        $workgroup = ServiceCommittee::workgroupsOnly()->findOrFail($id);
        Gate::authorize('update', $workgroup);

        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super admin');

        $rules = [
            'ar_name' => 'required|string|max:255',
            'en_name' => 'required|string|max:255',
            'workgroup_type' => 'required|in:permanent,temporary',
            'status' => 'required|in:active,completed,archived',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'chairman_name' => 'nullable|string|max:255',
            'chairman_phone' => 'nullable|string|max:255',
            'email' => 'required',
            'location' => 'nullable|string|max:255',
            'ar_address' => 'nullable|string|max:255',
            'en_address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'default_footer' => 'nullable|string|max:1000',
        ];

        if ($isSuperAdmin) {
            $rules['parent_id'] = 'required|exists:service_committees,id';
        }

        $request->validate($rules);

        $fields = $request->except('logo');

        if (!$isSuperAdmin) {
            // Keep existing parent_id unchanged for non-super-admins
            unset($fields['parent_id']);
        }

        if (isset($fields['email']) && is_numeric($fields['email'])) {
            $assignedUser = User::find((int)$fields['email']);
            if ($assignedUser) {
                $fields['user_id'] = $assignedUser->id;
                $fields['email'] = $assignedUser->email;
            }
        } elseif (isset($fields['email'])) {
            $assignedUser = User::where('email', $fields['email'])->first();
            if ($assignedUser) {
                $fields['user_id'] = $assignedUser->id;
            }
        }

        if (isset($assignedUser) && $assignedUser) {
            $assignedUser->assignRole('Workgroups');
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $fields['logo'] = $path;
        }

        $workgroup->update($fields);

        return redirect()->route('workgroup.index')->with('success', __('messages.Workgroup updated successfully'));
    }

    /**
     * Remove the specified workgroup from storage.
     */
    public function destroy(string $id)
    {
        $workgroup = ServiceCommittee::workgroupsOnly()->findOrFail($id);
        Gate::authorize('delete', $workgroup);

        $workgroup->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('workgroup.index')->with('success', __('messages.Workgroup deleted successfully'));
    }
}
