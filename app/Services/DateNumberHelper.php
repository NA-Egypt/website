<?php

namespace App\Services;

use Carbon\Carbon;

class DateNumberHelper
{
    /**
     * Convert Eastern Arabic numerals (٠-٩) to European numerals (0-9).
     */
    public static function toEuropeanNumerals($str)
    {
        if (is_null($str)) {
            return '';
        }
        $str = (string) $str;
        $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($eastern, $western, $str);
    }

    /**
     * Format a date with translatedFormat but convert digits back to European numerals.
     */
    public static function translatedFormat($date, $format)
    {
        if (!$date) {
            return '';
        }
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }
        
        $formatted = $date->translatedFormat($format);
        return self::toEuropeanNumerals($formatted);
    }
}
