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

        // Determine the field based on locale
        $field = app()->getLocale() === 'ar' ? 'ar_name' : 'en_name';

//        if (!empty($filters['day'])) {
//            $query->whereHas('day', function ($q) use ($filters, $field) {
//                $q->where($field, $filters['day']);
//            });
//        }

        // Default filter: Show meetings for the current day if no day filter is provided
        if (empty($filters['day'])) {
            $currentDay = Carbon::now()->englishDayOfWeek; // Get the current day name in English

            // If the locale is Arabic, convert the day name to Arabic
            if (app()->getLocale() === 'ar') {
                $currentDay = $this->convertDayToArabic($currentDay);
            }

            logger('Applying default filter for current day:', ['currentDay' => $currentDay]);

            $query->whereHas('day', function ($q) use ($currentDay, $field) {
                $q->where($field, $currentDay);
            });
        } elseif ($filters['day'] !== 'all') {
            // Apply the provided day filter (if not "all")
            logger('Applying custom day filter:', ['day' => $filters['day']]);

            $query->whereHas('day', function ($q) use ($filters, $field) {
                $q->where($field, $filters['day']);
            });
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