<?php

namespace App\Policies;

use App\Models\ServiceCommittee;
use App\Models\User;

class ServiceCommitteePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super admin')) {
            return true;
        }

        return null;
    }

    public function view(User $user, ServiceCommittee $serviceCommittee): bool
    {
        // Direct assigned user (Committee user or Workgroup user)
        if ($serviceCommittee->user_id === $user->id) {
            return true;
        }

        // Parent committee user viewing its child workgroup
        if ($serviceCommittee->isWorkgroup()) {
            if ($serviceCommittee->parent && $serviceCommittee->parent->user_id === $user->id) {
                return true;
            }
            if ($user->hasRole('rsc') && ($serviceCommittee->parent_id === 83 || ($serviceCommittee->parent && $serviceCommittee->parent->email === 'RSC@naegypt.org'))) {
                return true;
            }
        }

        if ($user->hasRole('rsc') && ($serviceCommittee->id === 83 || $serviceCommittee->email === 'RSC@naegypt.org')) {
            return true;
        }

        return false;
    }

    public function update(User $user, ServiceCommittee $serviceCommittee): bool
    {
        // Direct assigned user
        if ($serviceCommittee->user_id === $user->id) {
            return true;
        }

        // Parent committee user can edit/manage child workgroups
        if ($serviceCommittee->isWorkgroup()) {
            if ($serviceCommittee->parent && $serviceCommittee->parent->user_id === $user->id) {
                return true;
            }
            if ($user->hasRole('rsc') && ($serviceCommittee->parent_id === 83 || ($serviceCommittee->parent && $serviceCommittee->parent->email === 'RSC@naegypt.org'))) {
                return true;
            }
        }

        if ($user->hasRole('rsc') && ($serviceCommittee->id === 83 || $serviceCommittee->email === 'RSC@naegypt.org')) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super admin') || $user->hasRole('Committees') || $user->hasRole('rsc');
    }

    public function delete(User $user, ServiceCommittee $serviceCommittee): bool
    {
        // Only super admin or parent committee can delete a child workgroup
        if ($serviceCommittee->isWorkgroup()) {
            if ($serviceCommittee->parent && $serviceCommittee->parent->user_id === $user->id) {
                return true;
            }
            if ($user->hasRole('rsc') && ($serviceCommittee->parent_id === 83 || ($serviceCommittee->parent && $serviceCommittee->parent->email === 'RSC@naegypt.org'))) {
                return true;
            }
        }

        return false;
    }
}
