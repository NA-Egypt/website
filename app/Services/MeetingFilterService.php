<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Meeting;

class MeetingFilterService
{
    public function filterMeetings($filters, ?int $perPage = null)
    {
        $query = Meeting::with(['day', 'group.neighborhood.city', 'directOnlineGroup', 'options', 'topics']);
        $locale = app()->getLocale();
        // Determine the field based on locale
        $field = app()->getLocale() === 'ar' ? 'ar_name' : 'en_name';

        // Day filter logic
        if (isset($filters['day'])) {  // Check if day parameter exists (even if empty)
            if ($filters['day'] === 'all' || $filters['day'] === '') {
                // Explicitly show all days - no day filter applied
            } elseif (!empty($filters['day'])) {
                // Specific day selected: match ar_name or en_name
                $query->whereHas('day', fn($q) => $q->where('ar_name', $filters['day'])->orWhere('en_name', $filters['day']));
            }
        } else {
            // No default day filter - show all meetings on initial load
        }


        if (!empty($filters['city'])) {
            $query->whereHas('group.neighborhood.city', function ($q) use ($filters) {
                $q->where('ar_name', $filters['city'])->orWhere('en_name', $filters['city']);
            });
        }

        if (!empty($filters['serviceBody'])) {
            $query->whereHas('group.serviceBody', function ($q) use ($filters) {
                $q->where('ar_name', $filters['serviceBody'])->orWhere('en_name', $filters['serviceBody']);
            });
        }

        if (!empty($filters['group'])) {
            $query->whereHas('group', function ($q) use ($filters) {
                $q->where('ar_name', $filters['group'])->orWhere('en_name', $filters['group']);
            });
        }

        if (!empty($filters['neighborhood'])) {
            $query->whereHas('group.neighborhood', function ($q) use ($filters) {
                $q->where('ar_name', $filters['neighborhood'])->orWhere('en_name', $filters['neighborhood']);
            });
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

        if ($perPage !== null && $perPage > 0) {
            if (!empty($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            if (!empty($filters['recurrence']) && empty($filters['businessMeetingsOnly'])) {
                if ($filters['recurrence'] === 'weekly') {
                    // 'weekly' is the default "Weekly Meetings" filter which shows both weekly and monthly recurring meetings
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

            return $query->paginate($perPage);
        }

        if (!empty($filters['businessMeetingsOnly'])) {
            if (!empty($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            $meetings = $query->get();
            foreach ($meetings as $meeting) {
                $meeting->setRelation('recurring_variations', collect());
            }

            return $meetings;
        }

        return $this->mergeRecurringMeetings($query->get(), $filters);
    }

    /**
     * Merge same-group, same-day recurring (monthly/nth-week) meetings into their parent weekly meeting card,
     * and filter merged cards so a card is shown whenever either its primary schedule or an embedded
     * recurring variation matches the active type/recurrence filter.
     */
    public function mergeRecurringMeetings($candidates, array $filters = [])
    {
        $buckets = $candidates->groupBy(function (Meeting $meeting) {
            $groupKey = $meeting->group_id
                ? 'g_' . $meeting->group_id
                : ($meeting->direct_online_group_id ? 'dog_' . $meeting->direct_online_group_id : 'm_' . $meeting->id);

            return $groupKey . '_d_' . $meeting->day_id;
        });

        $mergedCards = collect();

        foreach ($buckets as $bucket) {
            if ($bucket->count() === 1) {
                $single = $bucket->first();
                $single->setRelation('recurring_variations', collect());
                $mergedCards->push($single);
                continue;
            }

            $weeklyMeetings = $bucket->filter(fn (Meeting $m) => $this->isWeeklyMeeting($m))->values();
            $monthlyMeetings = $bucket->reject(fn (Meeting $m) => $this->isWeeklyMeeting($m))->values();

            if ($weeklyMeetings->isNotEmpty() && $monthlyMeetings->isNotEmpty()) {
                foreach ($weeklyMeetings as $weekly) {
                    $weekly->setRelation('recurring_variations', collect());
                }

                foreach ($monthlyMeetings as $monthly) {
                    $monthlyStart = Carbon::parse($monthly->start_time)->format('H:i');
                    $parent = $weeklyMeetings->first(function (Meeting $w) use ($monthlyStart) {
                        return Carbon::parse($w->start_time)->format('H:i') === $monthlyStart;
                    }) ?? $weeklyMeetings->first();

                    $parent->recurring_variations->push($monthly);
                }

                foreach ($weeklyMeetings as $weekly) {
                    $mergedCards->push($weekly);
                }
            } elseif ($weeklyMeetings->isEmpty() && $monthlyMeetings->count() > 1) {
                // Multiple non-weekly schedules on the same day for the same group (e.g. 1st/3rd/4th/5th/last vs 2nd)
                $sorted = $monthlyMeetings->sortByDesc(fn (Meeting $m) => count($m->recurrence ?? []))->values();
                $primary = $sorted->first();
                $primary->setRelation('recurring_variations', $sorted->slice(1)->values());
                $mergedCards->push($primary);
            } else {
                foreach ($bucket as $meeting) {
                    $meeting->setRelation('recurring_variations', collect());
                    $mergedCards->push($meeting);
                }
            }
        }

        $hasActiveVariantFilter = !empty($filters['type'])
            || (!empty($filters['recurrence']) && $filters['recurrence'] !== 'weekly');

        return $mergedCards->filter(function (Meeting $card) use ($filters, $hasActiveVariantFilter) {
            $parentMatches = $this->matchesTypeAndRecurrence($card, $filters);
            $anyVariationMatches = false;

            foreach ($card->recurring_variations as $variation) {
                $varMatches = $this->matchesTypeAndRecurrence($variation, $filters);
                $variation->is_filter_match = $hasActiveVariantFilter && $varMatches;
                if ($varMatches) {
                    $anyVariationMatches = true;
                }
            }

            return $parentMatches || $anyVariationMatches;
        })->values();
    }

    public function isWeeklyMeeting(Meeting $meeting): bool
    {
        return empty($meeting->recurrence) || in_array('weekly', $meeting->recurrence, true);
    }

    protected function matchesTypeAndRecurrence(Meeting $meeting, array $filters): bool
    {
        if (!empty($filters['type']) && $meeting->type !== $filters['type']) {
            return false;
        }

        if (!empty($filters['recurrence']) && empty($filters['businessMeetingsOnly'])) {
            $recurrenceFilter = $filters['recurrence'];
            if ($recurrenceFilter === 'weekly') {
                return true;
            }

            if ($recurrenceFilter === 'monthly') {
                return !empty($meeting->recurrence)
                    && count(array_intersect($meeting->recurrence, ['1st', '2nd', '3rd', '4th', '5th', 'last'])) > 0;
            }

            return !empty($meeting->recurrence) && in_array($recurrenceFilter, $meeting->recurrence, true);
        }

        return true;
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