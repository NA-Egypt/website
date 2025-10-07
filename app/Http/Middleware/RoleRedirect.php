<?php

namespace App\Http\Middleware;

use App\Models\Group;
use App\Models\Meeting;
use App\Models\ServiceCommittee;
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

           if ($user->hasRole('Committees')) {
               return $this->handleCommittees($request, $next, $user);
           }

//            if ($user->hasRole('your_new_role')) {
//                return $this->handleYourNewRole($request, $next, $user);
//            }

            // Allow access to dashboard and general routes
            if (in_array($request->route()->getName(), ['dashboard'])) {
                return $next($request);
            }
            return redirect('/')->withErrors(['You do not have access.']);
        }

        return $next($request);
    }

   private function handleCommittees($request, $next, $user)
   {
       // Find the user's assigned service committee
       $serviceCommittee = ServiceCommittee::whereHas('user', function ($q) use ($user) {
           $q->where('email', $user->email);
       })->first();

       if ($serviceCommittee) {
           // For dashboard or initial access, redirect Committee users to their assigned committee
           if ($request->route()->getName() === 'dashboard') {
               return redirect()->route('serviceCommittee.show', ['serviceCommittee' => $serviceCommittee->id]);
           }

           $routeName = $request->route()->getName();
           $currentServiceCommitteeId = null;

           // Handle service committee routes
           if (in_array($routeName, ['serviceCommittee.show', 'serviceCommittee.edit', 'serviceCommittee.update'])) {
               $currentServiceCommittee = $request->route('serviceCommittee');
               $currentServiceCommitteeId = $currentServiceCommittee instanceof ServiceCommittee ? $currentServiceCommittee->id : $currentServiceCommittee;
           }

           // Allow access if the service committee matches
           if (
               in_array($routeName, [
                   'serviceCommittee.show',
                   'serviceCommittee.edit',
                   'serviceCommittee.update'
               ])
               && $currentServiceCommitteeId && $currentServiceCommitteeId == $serviceCommittee->id
           ) {
               return $next($request);
           }

           // Only redirect to service committee if accessing committee-specific routes
           if ($request->route()->getName() === 'dashboard') {
               return $next($request);
           }

           // Redirect to assigned service committee
           return redirect()->route('serviceCommittee.show', ['serviceCommittee' => $serviceCommittee->id]);
       }

       return redirect('/')->with('error', 'No service committee assigned');
   }

    private function handleGsr($request, $next, $user)
    {
        // Find the user's assigned group
        $group = Group::whereHas('user', function ($q) use ($user) {
            $q->where('email', $user->email);
        })->first();

        if ($group) {
            $routeName = $request->route()->getName();
            
            // For dashboard or initial access, redirect GSR to their assigned group
            if ($routeName === 'dashboard') {
                return redirect()->route('group.show', ['group' => $group->id]);
            }
            
            $currentGroupId = null;

            // Allow direct access to meeting.create (no group check needed)
            if ($routeName === 'meeting.create' || $routeName === 'meeting.store' ) {
                return $next($request);
            }

            // Handle group routes
            if (in_array($routeName, ['group.show', 'group.edit', 'group.update'])) {
                $currentGroup = $request->route('group');
                $currentGroupId = $currentGroup instanceof Group ? $currentGroup->id : $currentGroup;
            }
            // Handle meeting routes
            elseif (in_array($routeName, ['meeting.edit', 'meeting.update', 'meeting.destroy'])) {
                $meeting = $request->route('meeting');
                $meetingModel = $meeting instanceof Meeting ? $meeting : Meeting::find($meeting);

                if (!$meetingModel) {
                    \Log::error('Meeting not found:', ['meeting_id' => $meeting]);
                    return redirect('/')->with('error', 'Meeting not found');
                }

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

            // Only redirect to assigned group if they're trying to access group-specific routes
            // Allow dashboard and other general routes to proceed
            if ($request->route()->getName() === 'dashboard') {
                return $next($request);
            }

            // Redirect to assigned group for other routes
            return redirect()->route('group.show', ['group' => $group->id]);
        }

        return redirect('/')->with('error', 'No group assigned');
    }


}
