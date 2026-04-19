<?php

namespace App\Models;

use App\Traits\FormatedDateTime;
use App\Traits\MeetingDurationTrait;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{

    use FormatedDateTime;
    use MeetingDurationTrait;
    protected $fillable = [
        'group_id',
        'topic_id',
        'day_id',
        'start_time',
        'end_time',
        'notes',
        'type',
        'options',
        'lang',
        'status',
        'capacity'
    ];
    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function day() {
        return $this->belongsTo(Day::class);
    }

    public function topic() {
        return $this->belongsTo(Topic::class);
    }

    public function topics() {
        return $this->belongsToMany(Topic::class);
    }

    public function options()
    {
        return $this->belongsToMany(Option::class, 'meeting_option');
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

    // Accessor for time durations:
        public function getDurationAttribute()
    {
        return $this->calculateMeetingDuration($this->formatted_start_time, $this->formatted_end_time);
    }

}
