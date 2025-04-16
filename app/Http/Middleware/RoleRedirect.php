<?php

namespace App\Http\Middleware;

use App\Models\Group;
use App\Models\Meeting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleRedirect
{

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {

            if ($user->hasRole('super admin')) {
                // Full access
                return $next($request);
            }

            if ($user->hasRole('gsr')) {
                return $this->handleGsr($request, $next, $user);
            }

//            if ($user->hasRole('your_new_role')) {
//                return $this->handleYourNewRole($request, $next, $user);
//            }

            // fallback: no permission
            return redirect('/')->withErrors(['You do not have access.']);
        }

        return $next($request);
    }

    private function handleGsr($request, $next, $user)
    {
        // Find the user's assigned group
        $group = Group::whereHas('user', function ($q) use ($user) {
            $q->where('email', $user->email);
        })->first();

        if ($group) {
            $routeName = $request->route()->getName();
            $currentGroupId = null;

            // Handle group routes
            if (in_array($routeName, ['group.show', 'group.edit', 'group.update'])) {
                $currentGroup = $request->route('group');
                $currentGroupId = $currentGroup instanceof Group ? $currentGroup->id : $currentGroup;
            }
            // Handle meeting routes
            elseif (in_array($routeName, ['meeting.edit', 'meeting.update', 'meeting.destroy'])) {
                $meeting = $request->route('meeting');
                // Handle model binding or ID
                $meetingModel = $meeting instanceof Meeting ? $meeting : Meeting::find($meeting);
                if (!$meetingModel) {
                    \Log::error('Meeting not found:', ['meeting_id' => $meeting]);
                    return redirect('/')->with('error', 'Meeting not found');
                }
                // Access group_id (adjust based on your model)
                $currentGroupId = $meetingModel->group_id ?? ($meetingModel->group ? $meetingModel->group->id : null);
                if (!$currentGroupId) {
                    \Log::error('No group_id for meeting:', ['meeting_id' => $meetingModel->id]);
                    return redirect('/')->with('error', 'Invalid meeting group');
                }
            }

            // Allow access if the group matches
            if (
                in_array($routeName, [
                    'group.show',
                    'group.edit',
                    'group.update',
                    'meeting.edit',
                    'meeting.update',
                    'meeting.destroy'
                ])
                && $currentGroupId && $currentGroupId == $group->id
            ) {
                return $next($request);
            }

            // Redirect to assigned group
            return redirect()->route('group.show', ['group' => $group->id]);
        }

        return redirect('/')->with('error', 'No group assigned');
    }


//    private function handleYourNewRole($request, $next, $user)
//    {
//        // For example: allow only access to the 'meeting' routes
//        if ($request->routeIs('meeting.*')) {
//            return $next($request);
//        }
//
//        // If trying to access something else
//        return redirect()->route('meeting.index');
//    }

//    public function handle(Request $request, Closure $next)
//    {
//        $user = Auth::user();
//
//        if ($user) {
//            // Super Admin: allow full access
//            if ($user->hasRole('super admin')) {
//                return $next($request);
//            }
//
//            // GSR: redirect to assigned group
//            if ($user->hasRole('gsr')) {
//                $group = Group::whereHas('user', function ($q) use ($user) {
//                    $q->where('email', $user->email);
//                })->first();
//
//                if ($group) {
//                    $currentGroup = $request->route('group');
//                    $currentGroupId = $currentGroup instanceof Group ? $currentGroup->id : $currentGroup;
//
//                    if (!$request->routeIs('group.show') || $currentGroupId != $group->id) {
//                        return redirect()->route('group.show', ['group' => $group->id]);
//                    }
//                } else {
//                    return redirect('/');
//                }
//            }
//        }
//
//        return $next($request);
//    }
}
