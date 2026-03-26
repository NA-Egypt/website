<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Meeting;

class MeetingFilterService
{
    public function filterMeetings($filters)
    {
        $query = Meeting::query();
        $locale = app()->getLocale();
        // Determine the field based on locale
        $field = app()->getLocale() === 'ar' ? 'ar_name' : 'en_name';

        // Day filter logic
        if (isset($filters['day'])) {  // Check if day parameter exists (even if empty)
            if ($filters['day'] === 'all') {
                // Explicitly show all days - no day filter applied
            } elseif (!empty($filters['day'])) {
                // Specific day selected
                $query->whereHas('day', fn($q) => $q->where($field, $filters['day']));
            }
        } else {
            // Default: show current day's meetings only when NO filters are applied
            $hasAnyFilter = collect($filters)
                ->except('day')
                ->filter()
                ->isNotEmpty();

            if (!$hasAnyFilter) {
                $currentDay = Carbon::now()->englishDayOfWeek;
                if ($locale === 'ar') {
                    $currentDay = $this->convertDayToArabic($currentDay);
                }
                $query->whereHas('day', fn($q) => $q->where($field, $currentDay));
            }
        }


        if (!empty($filters['city'])) {
            $query->whereHas('group.neighborhood.city', function ($q) use ($filters, $field) {
                $q->where($field, $filters['city']);
            });
        }

        if (!empty($filters['serviceBody'])) {
            $query->whereHas('group.serviceBody', function ($q) use ($filters, $field) {
                $q->where($field, $filters['serviceBody']);
            });
        }

        if (!empty($filters['group'])) {
            $query->whereHas('group', function ($q) use ($filters, $field) {
                $q->where($field, $filters['group']);
            });
        }



        if (!empty($filters['neighborhood'])) {
            $query->whereHas('group.neighborhood', function ($q) use ($filters, $field) {
                $q->where($field, $filters['neighborhood']);
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']); // Assuming 'type' is a column in the meetings table
        }

        return $query->get();
    }

    private function convertDayToArabic($englishDay)
    {
        $dayMap = [
            'Monday' => 'الاثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد',
        ];

        return $dayMap[$englishDay] ?? $englishDay; // Fallback to English if mapping is missing
    }
}