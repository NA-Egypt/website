<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\ServiceBody;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Traits\PaginatesDataTables;

class UserController extends Controller
{
    use PaginatesDataTables;

    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $query = User::with(['roles', 'serviceBody']);

            if ($request->filled('role_name')) {
                $query->whereHas('roles', function($q) use ($request) {
                    $q->where('name', $request->input('role_name'));
                });
            }

            if ($request->filled('is_verified')) {
                if ($request->input('is_verified') === 'yes') {
                    $query->whereNotNull('email_verified_at');
                } elseif ($request->input('is_verified') === 'no') {
                    $query->whereNull('email_verified_at');
                }
            }

            $users = $this->paginateDataTable($query, $request, ['display_name', 'name', 'email', 'roles.name']);

            $locale = app()->getLocale();
            $users->getCollection()->transform(function($u) use ($locale) {
                $u->formatted_name = $u->display_name ?: $u->name;
                $u->is_verified = (bool) $u->email_verified_at;
                $u->roles_list = $u->roles ? $u->roles->pluck('name')->all() : [];
                $u->service_body_name = $u->serviceBody ? ($locale === 'ar' ? ($u->serviceBody->ar_name ?: $u->serviceBody->en_name) : ($u->serviceBody->en_name ?: $u->serviceBody->ar_name)) : null;
                $u->created_at_formatted = $u->created_at ? $u->created_at->format('Y-m-d H:i') : null;
                $u->is_super_admin = $u->hasRole('super admin');
                return $u;
            });

            return response()->json($users);
        }

        $kpiStats = [
            'total_users'           => User::count(),
            'verified_users'        => User::whereNotNull('email_verified_at')->count(),
            'unverified_users'      => User::whereNull('email_verified_at')->count(),
            'service_body_officers' => User::whereNotNull('service_body_id')->count(),
        ];

        $roles = Role::all(['id', 'name']);

        return view('users.index', [
            'users'    => collect(),
            'kpiStats' => $kpiStats,
            'roles'    => $roles
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $serviceBodies = ServiceBody::all();
        return view('users.create', compact('roles', 'permissions', 'serviceBodies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'display_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'roles' => 'array',
            'permissions' => 'array',
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
            $user->syncRoles($request->roles);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return redirect()->route('users.index')->with('success', __('messages.user_created_success'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $serviceBodies = ServiceBody::all();
        return view('users.edit', compact('user', 'roles', 'permissions', 'serviceBodies'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'display_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'array',
            'permissions' => 'array',
            'service_body_id' => 'nullable|exists:service_bodies,id',
        ]);

        $user->update([
            'name' => explode('@', $request->email)[0],
            'display_name' => $request->display_name,
            'email' => $request->email,
            'service_body_id' => $request->service_body_id,
        ]);

        $user->syncRoles($request->roles ?? []);
        $user->syncPermissions($request->permissions ?? []);
        return redirect()->route('users.index')->with('success', __('messages.user_updated_success'));
    }

    public function destroy(User $user, Request $request)
    {
        $user->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('messages.user_deleted')]);
        }

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

    public function impersonate(User $user)
    {
        $currentUser = Auth::user();

        // Security check: Only Super Admin can impersonate
        if (!$currentUser || !$currentUser->hasRole('super admin')) {
            return redirect()->route('dashboard')->with('error', __('messages.unauthorized_impersonation'));
        }

        // Security check: Cannot impersonate another Super Admin
        if ($user->hasRole('super admin')) {
            return redirect()->back()->with('error', __('messages.cannot_impersonate_admin'));
        }

        // Save original admin ID in session
        session(['impersonated_by' => $currentUser->id]);

        // Login as target user
        Auth::login($user);

        Log::info("Super Admin [ID: {$currentUser->id}] started impersonating User [ID: {$user->id}, Email: {$user->email}]");

        return redirect()->route('dashboard')->with('success', __('messages.impersonation_started', ['name' => $user->display_name ?? $user->name]));
    }

    public function stopImpersonating()
    {
        $adminId = session('impersonated_by');

        if (!$adminId) {
            return redirect()->route('dashboard');
        }

        $impersonatedUserId = Auth::id();

        // Restore original admin login
        Auth::loginUsingId($adminId);
        session()->forget('impersonated_by');

        Log::info("Super Admin [ID: {$adminId}] stopped impersonating User [ID: {$impersonatedUserId}]");

        return redirect()->route('users.index')->with('success', __('messages.impersonation_ended'));
    }
}
