<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start',
        'end',
        'description',
        'user_id',
        'color',
        'organizer',
        'location',
        'recurrence',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'recurrence' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor for Formatted Recurrence
    public function getFormattedRecurrenceAttribute()
    {
        if (empty($this->recurrence) || in_array('once', $this->recurrence)) {
            return __('messages.Once') ?? 'Once';
        }

        $weekday = $this->start ? \Carbon\Carbon::parse($this->start)->englishDayOfWeek : '';

        $formatted = array_map(function($item) use ($weekday) {
            if ($item === 'every_two_months') return __('messages.Every Two Months');
            if ($item === 'monthly') return __('messages.Monthly (Same Date)');
            if (in_array($item, ['1st', '2nd', '3rd', '4th', '5th', 'last'])) {
                $translatedItem = __('messages.' . $item);
                $translatedWeekday = $weekday ? __('messages.' . strtolower($weekday)) : '';
                return $translatedItem . ($translatedWeekday ? ' ' . $translatedWeekday : '');
            }
            // For weekly or once:
            return __('messages.' . ucfirst($item));
        }, $this->recurrence);

        return implode(', ', $formatted);
    }
}
