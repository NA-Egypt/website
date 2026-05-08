<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\CalendarEvent;

trait EventRecurrenceTrait
{
    /**
     * Generate occurrences of a single event between a start and end window.
     */
    public function generateOccurrences(CalendarEvent $event, $windowStart, $windowEnd)
    {
        $instances = [];
        $recurrences = $event->recurrence ?: [];
        
        $baseStart = Carbon::parse($event->start);
        $baseEnd = Carbon::parse($event->end);
        $duration = $baseStart->diffInSeconds($baseEnd);
        $windowStartDt = Carbon::parse($windowStart);
        $windowEndDt = Carbon::parse($windowEnd);

        // If no recurrence or 'once' is selected
        if (in_array('once', $recurrences) || empty($recurrences)) {
            if ($baseStart >= $windowStartDt && $baseStart <= $windowEndDt) {
                return [['start' => $baseStart, 'end' => $baseEnd]];
            }
            if ($baseStart < $windowStartDt && $baseEnd > $windowStartDt) { // Overlapping window
                return [['start' => $baseStart, 'end' => $baseEnd]];
            }
            return [];
        }

        // We only generate instances up to $windowEndDt or up to 2 years from baseStart (safety limit)
        $limitDate = min($windowEndDt->copy(), $baseStart->copy()->addYears(2));

        if (in_array('monthly', $recurrences)) {
            $current = $baseStart->copy();
            while ($current <= $limitDate) {
                if ($current >= $windowStartDt) {
                    $instances[] = ['start' => $current->copy(), 'end' => $current->copy()->addSeconds($duration)];
                }
                $current->addMonthNoOverflow();
            }
        } 
        elseif (in_array('every_two_months', $recurrences)) {
            $current = $baseStart->copy();
            while ($current <= $limitDate) {
                if ($current >= $windowStartDt) {
                    $instances[] = ['start' => $current->copy(), 'end' => $current->copy()->addSeconds($duration)];
                }
                $current->addMonthsNoOverflow(2);
            }
        }
        elseif (in_array('weekly', $recurrences)) {
            $current = $baseStart->copy();
            while ($current <= $limitDate) {
                if ($current >= $windowStartDt) {
                    $instances[] = ['start' => $current->copy(), 'end' => $current->copy()->addSeconds($duration)];
                }
                $current->addWeek();
            }
        }
        else {
            // Week of Month logic (1st, 2nd, 3rd, 4th, 5th, last)
            $startMonth = $baseStart->copy()->startOfMonth();
            $endMonth = $limitDate->copy()->endOfMonth();
            
            $currentMonth = $startMonth->copy();
            $allowed = ['1st', '2nd', '3rd', '4th', '5th', 'last'];
            $selectedNths = array_intersect($recurrences, $allowed);
            
            $nthMap = [
                '1st' => 'first',
                '2nd' => 'second',
                '3rd' => 'third',
                '4th' => 'fourth',
                '5th' => 'fifth',
                'last' => 'last'
            ];

            if (!empty($selectedNths)) {
                while ($currentMonth <= $endMonth) {
                    foreach ($selectedNths as $nth) {
                        $wordNth = $nthMap[$nth];
                        $dateStr = $wordNth . ' ' . $baseStart->englishDayOfWeek . ' of ' . $currentMonth->format('F Y');
                        try {
                            $targetDate = Carbon::parse($dateStr)->setTimeFrom($baseStart);
                            if ($targetDate->month == $currentMonth->month) {
                                if ($targetDate >= $baseStart && $targetDate >= $windowStartDt && $targetDate <= $limitDate) {
                                    $instances[] = ['start' => $targetDate->copy(), 'end' => $targetDate->copy()->addSeconds($duration)];
                                }
                            }
                        } catch (\Exception $e) {
                            // ignore parsing errors for unsupported combos
                        }
                    }
                    $currentMonth->addMonth();
                }
            }
        }

        // Sort by start date to ensure order
        usort($instances, function($a, $b) {
            return $a['start']->timestamp <=> $b['start']->timestamp;
        });

        // Filter duplicates if any
        $uniqueInstances = [];
        $seen = [];
        foreach ($instances as $inst) {
            $key = $inst['start']->timestamp;
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $uniqueInstances[] = $inst;
            }
        }

        return $uniqueInstances;
    }
}
