<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Meeting;

class MeetingFilterService
{
    public function filterMeetings($filters)
    {
        $query = Meeting::with(['day', 'group.neighborhood.city', 'directOnlineGroup', 'options', 'topics']);
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
            // No default day filter - show all meetings on initial load
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

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($bigSearch) use ($search) {
                $bigSearch->whereHas('group', function ($q) use ($search) {
                    $q->where('ar_name', 'LIKE', '%' . $search . '%')
                      ->orWhere('en_name', 'LIKE', '%' . $search . '%')
                      ->orWhere('ar_address', 'LIKE', '%' . $search . '%')
                      ->orWhere('en_address', 'LIKE', '%' . $search . '%');
                })->orWhereHas('directOnlineGroup', function ($q) use ($search) {
                    $q->where('ar_name', 'LIKE', '%' . $search . '%')
                      ->orWhere('en_name', 'LIKE', '%' . $search . '%');
                });
            });
        }

        if (!empty($filters['virtualOnly'])) {
            $query->where(function ($bigQ) {
                $bigQ->whereHas('group', function ($q) {
                    $q->whereIn('group_type', ['اونلاين', 'اون لاين', 'online'])
                      ->where(function ($sub) {
                          $sub->whereNull('location')
                              ->orWhere(function ($sub2) {
                                  $sub2->where('location', 'not like', '%map%')
                                       ->where('location', 'not like', '%goo.gl%');
                              });
                      });
                })->orWhereNotNull('direct_online_group_id');
            });
        } else {
            $query->where(function ($q) {
                $q->whereNull('direct_online_group_id')
                  ->where(function ($subQ) {
                      $subQ->whereDoesntHave('group')
                           ->orWhereHas('group', function ($sub) {
                               $sub->whereNotIn('group_type', ['اونلاين', 'اون لاين', 'online']);
                           });
                  });
            });
        }

        if (!empty($filters['recurrence']) && empty($filters['businessMeetingsOnly'])) {
            if ($filters['recurrence'] === 'weekly') {
                // 'weekly' is the default "Weekly Meetings" filter which should now show both weekly and monthly recurring meetings
            } elseif ($filters['recurrence'] === 'monthly') {
                $query->where(function($q) {
                    $q->whereNotNull('recurrence')
                      ->where(function($sub) {
                          foreach (['1st', '2nd', '3rd', '4th', '5th', 'last'] as $item) {
                              $sub->orWhere('recurrence', 'like', '%"' . $item . '"%');
                          }
                      });
                });
            } else {
                $query->where('recurrence', 'like', '%"' . $filters['recurrence'] . '"%');
            }
        }

        if (!empty($filters['englishOnly'])) {
            $query->where('lang', 'english');
        }

        if (!empty($filters['businessMeetingsOnly'])) {
            $query->whereHas('topics', function($q) {
                $q->where('en_name', 'Group Business Meeting');
            });
        } else {
            $query->whereDoesntHave('topics', function($q) {
                $q->where('en_name', 'Group Business Meeting');
            });
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