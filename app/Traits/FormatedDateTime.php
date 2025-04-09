<?php

namespace App\Traits;

use Carbon\Carbon;

trait FormatedDateTime
{
    public function formatStartTime($startTime)
    {
        return Carbon::parse($startTime)->format('g:i A');
    }

    public function formatEndTime($endTime)
    {
        return Carbon::parse($endTime)->format('g:i A');
    }

    public function formatDate($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }
}
