<?php

namespace App\Services;

use App\Models\City;
use App\Models\Group;
use App\Models\DirectOnlineGroup;
use App\Models\Meeting;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;

class StatsService
{
    /**
     * Cache TTL in seconds (30 minutes).
     */
    protected int $ttl = 1800;

    /**
     * Get system statistics (cached).
     *
     * @param bool $refresh
     * @return array
     */
    public function getStats(bool $refresh = false): array
    {
        $cacheKey = 'na_egypt_public_stats';

        if ($refresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, $this->ttl, function () {
            return $this->computeStats();
        });
    }

    /**
     * Compute statistics from database.
     *
     * @return array
     */
    protected function computeStats(): array
    {
        $weeklyMeetings = Meeting::notMonthlyRecurrent()->inPersonOnly()->where('status', 'available')->count();
        $totalMeetings = Meeting::where('status', 'available')->count();
        $inPersonGroups = Group::inPersonOnly()->count();
        $directOnlineGroups = DirectOnlineGroup::count();
        $totalGroups = $inPersonGroups + $directOnlineGroups;
        $governorates = City::count();

        $upcomingEvents = 0;
        try {
            if (class_exists(\App\Models\CalendarEvent::class)) {
                $upcomingEvents = \App\Models\CalendarEvent::where('start', '>=', now())->count();
            }
        } catch (\Exception $e) {
            $upcomingEvents = 0;
        }

        return [
            'weekly_meetings' => $weeklyMeetings,
            'total_meetings' => $totalMeetings,
            'in_person_groups' => $inPersonGroups,
            'online_groups' => $directOnlineGroups,
            'groups' => $inPersonGroups, // Matching frontpage primary count
            'total_groups' => $totalGroups,
            'governorates' => $governorates,
            'cities' => $governorates,
            'upcoming_events' => $upcomingEvents,
        ];
    }
}
