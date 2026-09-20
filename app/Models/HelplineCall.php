<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelplineCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'duration',
        'call_date',
        'call_time_shift',
        'caller_type',
        'caller_type_other',
        'referral_source',
        'referral_source_other',
        'hospital_name',
        'poster_location',
        'volunteer_id',
        'volunteer_name',
        'volunteer_name_other',
        'is_step_12',
        'call_brief',
        'discuss_in_meeting',
        'additional_info',
        'entry_time',
    ];

    protected $casts = [
        'call_date' => 'date',
        'is_step_12' => 'boolean',
        'discuss_in_meeting' => 'boolean',
        'entry_time' => 'datetime',
    ];

    protected $appends = [
        'effective_volunteer_name',
        'effective_caller_type',
        'effective_referral_source',
        'duration_label',
    ];

    public function volunteer()
    {
        return $this->belongsTo(HelplineVolunteer::class, 'volunteer_id');
    }

    public function getEffectiveVolunteerNameAttribute(): string
    {
        if ($this->volunteer_name === 'أخرى' || $this->volunteer_name === 'Other') {
            return $this->volunteer_name_other ?: $this->volunteer_name;
        }
        return $this->volunteer ? $this->volunteer->name : $this->volunteer_name;
    }

    public function getEffectiveCallerTypeAttribute(): string
    {
        if (($this->caller_type === 'أخرى' || $this->caller_type === 'Other') && !empty($this->caller_type_other)) {
            return $this->caller_type_other;
        }
        return $this->caller_type;
    }

    public function getEffectiveReferralSourceAttribute(): string
    {
        if (($this->referral_source === 'أخرى' || $this->referral_source === 'Other') && !empty($this->referral_source_other)) {
            return $this->referral_source_other;
        }
        return $this->referral_source;
    }

    public function getDurationLabelAttribute(): string
    {
        return $this->duration === 'less_than_5' ? 'أقل من 5 دقائق' : 'أكثر من 5 دقائق';
    }

    public function scopeInDateRange($query, $from, $to)
    {
        if ($from) {
            $query->where('entry_time', '>=', $from);
        }
        if ($to) {
            $query->where('entry_time', '<=', $to);
        }
        return $query;
    }
}
