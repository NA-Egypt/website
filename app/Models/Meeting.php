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
        'direct_online_group_id',
        'topic_id',
        'day_id',
        'start_time',
        'end_time',
        'notes',
        'type',
        'options',
        'lang',
        'status',
        'recurrence'
    ];
    
    protected $casts = [
        'recurrence' => 'array',
    ];
    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function directOnlineGroup() {
        return $this->belongsTo(DirectOnlineGroup::class);
    }

    public function getGroupOrDirectAttribute()
    {
        return $this->group ?: $this->directOnlineGroup;
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

    // Accessor for Formatted Recurrence:
    public function getFormattedRecurrenceAttribute()
    {
        if (empty($this->recurrence) || in_array('weekly', $this->recurrence)) {
            return __('messages.Weekly');
        }

        $formatted = array_map(function($item) {
            $translated = __('messages.' . $item);
            return $translated !== 'messages.' . $item ? $translated : ucfirst($item);
        }, $this->recurrence);

        return implode(', ', $formatted);
    }

    public function scopeNotMonthlyRecurrent($query)
    {
        return $query->where(function($q) {
            $q->whereNull('recurrence')
              ->orWhere(function($sub) {
                  foreach (['1st', '2nd', '3rd', '4th', '5th', 'last'] as $item) {
                      $sub->where('recurrence', 'not like', '%"' . $item . '"%');
                  }
              });
        })->whereDoesntHave('topics', function($q) {
            $q->where('en_name', 'Group Business Meeting');
        });
    }

    public function getNextOccurrence()
    {
        if (!$this->day || !$this->start_time) {
            return null;
        }

        $now = \Carbon\Carbon::now();
        $dayName = $this->day->en_name; // e.g. 'Sunday'

        if (empty($dayName) || !in_array(strtolower($dayName), ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])) {
            return null;
        }
        $startTime = $this->start_time; // e.g. '19:30:00'

        // If recurrence is empty or contains 'weekly', the calculation is simple:
        if (empty($this->recurrence) || in_array('weekly', $this->recurrence)) {
            // Find next occurrence of that weekday:
            $next = \Carbon\Carbon::parse("next {$dayName} {$startTime}");
            // If today is the weekday and the start time is in the future, we use today:
            $today = \Carbon\Carbon::parse("today {$startTime}");
            if ($now->dayOfWeek === $today->dayOfWeek && $now->lt($today)) {
                return $today;
            }
            return $next;
        }

        // If specific weeks of the month are defined:
        $candidates = [];

        foreach ([0, 1] as $monthOffset) {
            $month = $now->copy()->addMonths($monthOffset);
            
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            // Find the first occurrence of $dayName in the month:
            $firstOccurrence = $startOfMonth->copy();
            while ($firstOccurrence->format('l') !== $dayName) {
                $firstOccurrence->addDay();
            }

            $occurrences = [];
            $temp = $firstOccurrence->copy();
            while ($temp->lte($endOfMonth)) {
                $occurrences[] = $temp->copy();
                $temp->addWeek();
            }

            foreach ($this->recurrence as $rec) {
                $candidateDate = null;
                if ($rec === '1st' && isset($occurrences[0])) {
                    $candidateDate = $occurrences[0];
                } elseif ($rec === '2nd' && isset($occurrences[1])) {
                    $candidateDate = $occurrences[1];
                } elseif ($rec === '3rd' && isset($occurrences[2])) {
                    $candidateDate = $occurrences[2];
                } elseif ($rec === '4th' && isset($occurrences[3])) {
                    $candidateDate = $occurrences[3];
                } elseif ($rec === '5th' && isset($occurrences[4])) {
                    $candidateDate = $occurrences[4];
                } elseif ($rec === 'last' && count($occurrences) > 0) {
                    $candidateDate = end($occurrences);
                }

                if ($candidateDate) {
                    $timeParts = explode(':', $startTime);
                    $candidateDateTime = $candidateDate->copy()->setTime($timeParts[0], $timeParts[1] ?? 0, $timeParts[2] ?? 0);
                    $candidates[] = $candidateDateTime;
                }
            }
        }

        usort($candidates, function($a, $b) {
            return $a->timestamp <=> $b->timestamp;
        });

        foreach ($candidates as $candidate) {
            if ($candidate->gt($now)) {
                return $candidate;
            }
        }

        return null;
    }
}

