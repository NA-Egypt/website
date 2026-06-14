<?php

namespace App\Http\Middleware;

use App\Models\Group;
use App\Models\ServiceCommittee;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleRedirect
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $request->route()->getName() === 'dashboard') {
            if ($user->roles->isEmpty()) {
                return redirect()->route('frontend.home');
            }

            if ($user->hasRole('super admin')) {
                return $next($request);
            }

            if ($user->hasRole('gsr')) {
                $group = Group::where('user_id', $user->id)->first();
                if ($group) {
                    return redirect()->route('group.show', ['group' => $group->id]);
                }
            }

            if ($user->hasRole('Committees')) {
                $serviceCommittee = ServiceCommittee::where('user_id', $user->id)->first();
                if ($serviceCommittee) {
                    return redirect()->route('serviceCommittee.show', ['serviceCommittee' => $serviceCommittee->id]);
                }
            }
        }

        return $next($request);
    }
}
