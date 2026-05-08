<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $fillable = [
        'group_id',
        'meetings_per_week',
        'agenda_date',
        'service_position',
        'submitter_name',
        'alt_gsr_position',
        'alt_gsr_name',
        'new_comers',
        'open_positions',
        'next_business_meeting',
        'recovery_meetings_changes',
        'recovery_atmosphere',
        'trusted_servants',
        'financial_issues',
        'other_topics'
    ];

    protected $casts = [
        'agenda_date' => 'date',
        'next_business_meeting' => 'datetime',
        'recovery_meetings_changes' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
