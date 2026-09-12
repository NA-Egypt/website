<?php

namespace App\Http\Controllers;

use App\Models\ServiceCommittee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use App\Traits\PaginatesDataTables;

class ServiceCommitteeController extends Controller implements HasMiddleware
{
    use PaginatesDataTables;

    public static function middleware(): array
    {
        return [
            new Middleware('role:super admin', only: ['index', 'create', 'store', 'destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = ServiceCommittee::committeesOnly()->with(['workgroups', 'user'])->withCount('workgroups');

            if ($request->filled('has_workgroups')) {
                if ($request->input('has_workgroups') === 'yes') {
                    $query->has('workgroups');
                } elseif ($request->input('has_workgroups') === 'no') {
                    $query->doesntHave('workgroups');
                }
            }

            $serviceCommittees = $this->paginateDataTable($query, $request, ['ar_name', 'en_name', 'email', 'chairman_name', 'chairman_phone']);

            $locale = app()->getLocale();
            $serviceCommittees->getCollection()->transform(function($item) use ($locale) {
                $item->primary_name = $locale === 'ar' ? ($item->ar_name ?: $item->en_name) : ($item->en_name ?: $item->ar_name);
                $item->secondary_name = $locale === 'ar' ? $item->en_name : $item->ar_name;
                $item->workgroups_count = $item->workgroups_count ?? 0;
                $item->workgroups_list = $item->workgroups ? $item->workgroups->map(function($w) use ($locale) {
                    return [
                        'id' => $w->id,
                        'name' => $locale === 'ar' ? ($w->ar_name ?: $w->en_name) : ($w->en_name ?: $w->ar_name),
                        'status' => $w->status,
                        'type' => $w->workgroup_type
                    ];
                })->values()->all() : [];
                return $item;
            });

            return response()->json($serviceCommittees);
        }

        $kpiStats = [
            'total_committees'  => ServiceCommittee::committeesOnly()->count(),
            'total_workgroups'  => ServiceCommittee::workgroupsOnly()->count(),
            'active_committees' => ServiceCommittee::committeesOnly()->active()->count(),
            'officers_count'    => ServiceCommittee::committeesOnly()->whereNotNull('user_id')->count(),
        ];

        return view('serviceCommittee.index', [
            'serviceCommittees' => collect(),
            'kpiStats'          => $kpiStats
        ]);
    }

    public function __invoke()
    {
        $serviceCommittees = ServiceCommittee::committeesOnly()
            ->with(['workgroups' => function ($q) {
                $q->active()->orderBy('workgroup_type')->orderBy('ar_name');
            }])
            ->get();

        $allWorkgroups = ServiceCommittee::workgroupsOnly()
            ->active()
            ->with('parent')
            ->orderBy('workgroup_type')
            ->orderBy('ar_name')
            ->get();

        return view('frontend.comms', [
            'serviceCommittees' => $serviceCommittees,
            'allWorkgroups' => $allWorkgroups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();

        return view('serviceCommittee.create', ['users'=>$users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ar_name' => 'required|string|max:255',
            'en_name' => 'required|string|max:255',
            'chairman_name' => 'nullable|string|max:255',
            'chairman_phone' => 'nullable|string|max:255',
            'email' => 'required',
            'location' => 'nullable|string|max:255',
            'ar_address' => 'nullable|string|max:255',
            'en_address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'default_footer' => 'nullable|string|max:1000',
        ]);

        $fields = $request->except('logo');

        if (isset($fields['email']) && is_numeric($fields['email'])) {
            $user = User::find((int)$fields['email']);
            if ($user) {
                $fields['user_id'] = $user->id;
                $fields['email'] = $user->email;
            }
        } elseif (isset($fields['email'])) {
            $user = User::where('email', $fields['email'])->first();
            if ($user) {
                $fields['user_id'] = $user->id;
            }
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $fields['logo'] = $path;
        }

        ServiceCommittee::create($fields);

        return redirect()->route('serviceCommittee.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $serviceCommittee = ServiceCommittee::findOrFail($id);
        Gate::authorize('view', $serviceCommittee);

        return view('serviceCommittee.show', ['serviceCommittee' => $serviceCommittee]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceCommittee $serviceCommittee)
    {
        Gate::authorize('update', $serviceCommittee);
        $users = User::all();
        return view('serviceCommittee.edit', ['serviceCommittee' => $serviceCommittee, 'users' => $users]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceCommittee $serviceCommittee)
    {
        Gate::authorize('update', $serviceCommittee);
        
        $request->validate([
            'ar_name' => 'required|string|max:255',
            'en_name' => 'required|string|max:255',
            'chairman_name' => 'nullable|string|max:255',
            'chairman_phone' => 'nullable|string|max:255',
            'email' => 'required',
            'location' => 'nullable|string|max:255',
            'ar_address' => 'nullable|string|max:255',
            'en_address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'default_footer' => 'nullable|string|max:1000',
        ]);

        $fields = $request->except('logo');
        if (isset($fields['email']) && is_numeric($fields['email'])) {
            $user = User::find((int)$fields['email']);
            if ($user) {
                $fields['user_id'] = $user->id;
                $fields['email'] = $user->email;
            }
        } elseif (isset($fields['email'])) {
            $user = User::where('email', $fields['email'])->first();
            if ($user) {
                $fields['user_id'] = $user->id;
            }
        }

        if ($request->hasFile('logo')) {
            if ($serviceCommittee->logo) {
                Storage::disk('public')->delete($serviceCommittee->logo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $fields['logo'] = $path;
        }

        $serviceCommittee->update($fields);

        return redirect()->route('serviceCommittee.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCommittee $serviceCommittee, Request $request)
    {
        $serviceCommittee->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('messages.service_committee_deleted_success')]);
        }

        return redirect()->route('serviceCommittee.index')->with('success', __('messages.service_committee_deleted_success'));
    }
}
