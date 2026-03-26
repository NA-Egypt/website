<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScMeeting extends Model
{
    protected $fillable = ['service_committee_id', 'week_number', 'day_id', 'time', 'notes'];

    public function serviceCommittee()
    {
        return $this->belongsTo(ServiceCommittee::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

}
