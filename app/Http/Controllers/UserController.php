<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
        ]);

        $user->roles()->sync($request->roles);
        return redirect()->route('users.index')->with('success', 'User roles updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User Deleted!');
    }
}
