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
        'other_topics' => 'array',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get translated service position name.
     */
    public function getTranslatedServicePositionAttribute()
    {
        $position = $this->service_position;
        if (empty($position)) {
            return '-';
        }
        switch ($position) {
            case 'Open Position':
                return __('messages.open_position') ?? 'ترشيحات';
            case 'Alt. GSR':
                return __('messages.alt_gsr') ?? 'نائب ممثل المجموعة';
            case 'GSR':
                return __('messages.gsr') ?? 'ممثل المجموعة';
            default:
                return $position;
        }
    }

    /**
     * Get translated alt GSR position name.
     */
    public function getTranslatedAltGsrPositionAttribute()
    {
        $position = $this->alt_gsr_position;
        if (empty($position)) {
            return null;
        }
        switch ($position) {
            case 'Open Position':
                return __('messages.open_position') ?? 'ترشيحات';
            case 'Alt. GSR':
                return __('messages.alt_gsr') ?? 'نائب ممثل المجموعة';
            default:
                return $position;
        }
    }
}
