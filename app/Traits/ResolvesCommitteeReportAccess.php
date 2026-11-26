<?php

namespace App\Traits;

use App\Models\ServiceCommittee;
use Illuminate\Support\Facades\Auth;

trait ResolvesCommitteeReportAccess
{
    protected function getServiceCommittee()
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        if ($user->hasRole('rsc')) {
            $rscCommittee = ServiceCommittee::find(83) ?: ServiceCommittee::where('email', 'RSC@naegypt.org')->first();
            if ($rscCommittee) {
                return $rscCommittee;
            }
        }

        return ServiceCommittee::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();
    }

    protected function isRsc()
    {
        if (! Auth::check()) {
            return false;
        }
        $user = Auth::user();

        return $user->hasRole('super admin') || $user->hasRole('rsc');
    }

    protected function isRestrictedConsumer($user)
    {
        if (! $user) {
            return true;
        }

        return $user->hasRole('ServiceBody') || $user->hasRole('gsr');
    }
}
