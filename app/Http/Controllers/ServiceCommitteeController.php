<?php

namespace App\Http\Controllers;

use App\Models\ServiceCommittee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ServiceCommitteeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:super admin', only: ['index', 'create', 'store', 'destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ServiceCommittee = ServiceCommittee::all();

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
        $fields = request()->all();

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
        $serviceCommittee->update($request->all());

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
