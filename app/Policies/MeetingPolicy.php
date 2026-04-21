<?php

namespace App\Policies;

use App\Models\Meeting;
use App\Models\User;

class MeetingPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('gsr');
    }

    public function update(User $user, Meeting $meeting): bool
    {
        return $user->hasRole('gsr') && $meeting->group && $meeting->group->user_id === $user->id;
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        return $this->update($user, $meeting);
    }
}
