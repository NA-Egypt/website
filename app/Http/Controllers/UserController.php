<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\ServiceBody;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'serviceBody'])->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $serviceBodies = ServiceBody::all();
        return view('users.create', compact('roles', 'serviceBodies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'display_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'roles' => 'array',
            'service_body_id' => 'nullable|exists:service_bodies,id',
        ]);

        $user = User::create([
            'name' => explode('@', $request->email)[0],
            'display_name' => $request->display_name,
            'email' => $request->email,
            'type' => 'manual',
            'service_body_id' => $request->service_body_id,
        ]);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return redirect()->route('users.index')->with('success', __('messages.user_created_success'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $serviceBodies = ServiceBody::all();
        return view('users.edit', compact('user', 'roles', 'serviceBodies'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'display_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'array',
            'service_body_id' => 'nullable|exists:service_bodies,id',
        ]);

        $user->update([
            'name' => explode('@', $request->email)[0],
            'display_name' => $request->display_name,
            'email' => $request->email,
            'service_body_id' => $request->service_body_id,
        ]);

        $user->roles()->sync($request->roles);
        return redirect()->route('users.index')->with('success', __('messages.user_updated_success'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', __('messages.user_deleted'));
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $userIds = $request->input('user_ids', []);

        if (empty($userIds)) {
            return redirect()->route('users.index')->with('error', __('messages.no_users_selected'));
        }

        if ($action === 'delete') {
            $users = User::whereIn('id', $userIds)->get();
            /** @var \App\Models\User $user */
            foreach ($users as $user) {
                $user->delete();
            }
            return redirect()->route('users.index')->with('success', __('messages.selected_users_deleted'));
        }

        return redirect()->route('users.index')->with('error', __('messages.invalid_action'));
    }
}
