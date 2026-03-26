<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait MeetingDurationTrait
{
    /**
     * Calculate the duration between two times and return it in localized format.
     *
     * @param string $startTime (formatted as "H:i A", e.g., "08:00 PM")
     * @param string $endTime (formatted as "H:i A", e.g., "09:30 PM")
     * @return string
     */
    public function calculateMeetingDuration($startTime, $endTime)
    {
        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);
        $durationInSeconds = $endTimestamp - $startTimestamp;

        if ($durationInSeconds <= 0) {
            return __('messages.invalid_duration');
        }

        // Extract hours and minutes
        $hours = (int) gmdate('H', $durationInSeconds);
        $minutes = (int) gmdate('i', $durationInSeconds);

        // Ensure minutes are always shown as two digits
        $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        // Get the current locale
        $locale = App::getLocale();

        if ($locale === 'ar') {
            return $this->formatDurationArabic($hours, $formattedMinutes);
        }

        return $this->formatDurationEnglish($hours, $formattedMinutes);
    }

    /**
     * Format duration in Arabic
     */
    private function formatDurationArabic($hours, $minutes)
    {
        $hourText = $hours === 1 ? 'ساعة' : 'ساعات';

        return "$hours $hourText و $minutes دقيقة";
    }

    /**
     * Format duration in English
     */
    private function formatDurationEnglish($hours, $minutes)
    {
        $hourText = $hours === 1 ? 'hour' : 'hours';

        return "$hours $hourText and $minutes minutes";
    }
}

