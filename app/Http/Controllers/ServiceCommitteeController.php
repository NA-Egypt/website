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
            $query = ServiceCommittee::query();
            $ServiceCommittee = $this->paginateDataTable($query, $request, ['ar_name', 'en_name', 'email', 'chairman_name', 'chairman_phone']);
            return response()->json($ServiceCommittee);
        }

        $ServiceCommittee = collect();
        return view('serviceCommittee.index', ['ServiceCommittee'=>$ServiceCommittee]);
    }

    public function __invoke()
    {
        $serviceCommittees = ServiceCommittee::all();

        return view('frontend.comms', ['serviceCommittees'=>$serviceCommittees]);
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
    public function destroy(ServiceCommittee $serviceCommittee)
    {
        $serviceCommittee->delete();

        return redirect()->route('serviceCommittee.index');
    }
}
