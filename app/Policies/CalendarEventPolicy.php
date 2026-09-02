<?php

namespace App\Policies;

use App\Models\CalendarEvent;
use App\Models\User;

class CalendarEventPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, CalendarEvent $calendarEvent): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create calendar events')
            || $user->hasRole('super admin')
            || $user->hasRole('rsc')
            || $user->hasRole('Committees')
            || $user->hasRole('ServiceBody');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CalendarEvent $calendarEvent): bool
    {
        if ($user->hasRole('super admin') || $user->hasRole('rsc') || $user->can('manage calendar events')) {
            return true;
        }

        // Creator can update their own event if they have permission to create events
        return $this->create($user) && $calendarEvent->user_id && (int)$calendarEvent->user_id === (int)$user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CalendarEvent $calendarEvent): bool
    {
        if ($user->hasRole('super admin') || $user->hasRole('rsc') || $user->can('manage calendar events')) {
            return true;
        }

        // Creator can delete their own event if they have permission to create events
        return $this->create($user) && $calendarEvent->user_id && (int)$calendarEvent->user_id === (int)$user->id;
    }
}
