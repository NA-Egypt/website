<?php

namespace App\Policies;

use App\Models\ServiceCommittee;
use App\Models\User;

class ServiceCommitteePolicy
{
    public function view(User $user, ServiceCommittee $serviceCommittee): bool
    {
        return $user->hasRole('Committees') && $serviceCommittee->user_id === $user->id;
    }

    public function update(User $user, ServiceCommittee $serviceCommittee): bool
    {
        return $this->view($user, $serviceCommittee);
    }
}
