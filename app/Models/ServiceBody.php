<?php

namespace App\Models;

use App\Traits\FormatedDateTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ServiceBody extends Model
{
    use FormatedDateTime;
    protected $fillable =[

        'ar_name',
        'en_name',
        'description',
        'day_id',
        'date',
        'start_time',
        'end_time',
        'location'
    ];

    public function groups() {
        return $this->hasMany(Group::class);
    }

    public function meetings() {
        return $this->hasManyThrough(Meeting::class, Group::class);
    }

    public function agendas() {
        return $this->hasManyThrough(Agenda::class, Group::class);
    }

    public function day() {
        return $this->belongsTo(Day::class);
    }

    public function events() {
        return $this->hasMany(Event::class);
    }

    // Accessor for End Time Formatted [from trait]:
    public function getFormattedStartTimeAttribute()
    {
        return $this->formatStartTime($this->start_time);
    }

    // Accessor for End Time Formatted [from trait]:
    public function getFormattedEndTimeAttribute()
    {
        return $this->formatEndTime($this->end_time);
    }

    // Accessor for Date Formatted:
    public function getFormattedDateAttribute()
    {
        return $this->formatDate($this->date);
    }
    
}
