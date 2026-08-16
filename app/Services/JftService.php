<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class JftService
{
    /**
     * Get the Just For Today reading for a specific date or today.
     *
     * @param string|null $dateString YYYY-MM-DD or null
     * @return array
     */
    public function getReading(?string $dateString = null): array
    {
        $timezone = config('app.timezone', 'Africa/Cairo');

        if (!empty($dateString)) {
            try {
                $date = Carbon::parse($dateString, $timezone);
            } catch (\Exception $e) {
                $date = Carbon::now($timezone);
            }
        } else {
            $date = Carbon::now($timezone);
        }

        $cacheKey = 'jft_reading_' . $date->format('Y_m_d');

        return Cache::remember($cacheKey, 3600, function () use ($date) {
            return $this->parseReadingFile($date);
        });
    }

    /**
     * Parse the JFT HTML file for a given Carbon date.
     *
     * @param Carbon $date
     * @return array
     */
    protected function parseReadingFile(Carbon $date): array
    {
        $day = $date->day;
        $monthShort = strtolower($date->format('M')); // jan, feb, mar...

        $fileName = "{$day}_{$monthShort}_.html";
        $filePath = public_path("literature/jft/{$fileName}");

        if (!file_exists($filePath)) {
            if ($monthShort === 'feb' && $day == 29) {
                $filePath = public_path("literature/jft/28_feb_.html");
            }
        }

        if (!file_exists($filePath)) {
            return [
                'date' => $date->format('Y-m-d'),
                'page_date' => $date->translatedFormat('j F'),
                'title' => '',
                'quote' => '',
                'quote_source' => '',
                'content' => [],
                'thought_for_the_day' => '',
                'content_html' => '',
            ];
        }

        $rawHtml = file_get_contents($filePath);
        return $this->extractDataFromHtml($rawHtml, $date);
    }

    /**
     * Extract structured fields from the raw JFT HTML content.
     *
     * @param string $html
     * @param Carbon $date
     * @return array
     */
    protected function extractDataFromHtml(string $html, Carbon $date): array
    {
        $bodyHtml = '';
        if (preg_match('/<body>(.*?)<\/body>/is', $html, $matches)) {
            $bodyHtml = trim($matches[1]);
        } else {
            $bodyHtml = trim($html);
        }

        $title = '';
        if (preg_match('/<h1>(.*?)<\/h1>/is', $bodyHtml, $matches)) {
            $title = trim(strip_tags($matches[1]));
        }

        $pageDate = '';
        if (preg_match('/<h2>(.*?)<\/h2>/is', $bodyHtml, $matches)) {
            $pageDate = trim(strip_tags($matches[1]));
        }

        $paragraphs = [];
        if (preg_match_all('/<p>(.*?)<\/p>/is', $bodyHtml, $matches)) {
            foreach ($matches[1] as $p) {
                $cleaned = trim(strip_tags($p));
                if (!empty($cleaned)) {
                    $paragraphs[] = $cleaned;
                }
            }
        }

        $quote = '';
        $quoteSource = '';
        $thoughtForTheDay = '';
        $contentParagraphs = [];

        $totalP = count($paragraphs);
        if ($totalP > 0) {
            $quote = $paragraphs[0];
            
            if ($totalP > 1) {
                $quoteSource = $paragraphs[1];
            }

            $lastIndex = $totalP - 1;
            if ($lastIndex >= 2) {
                $lastP = $paragraphs[$lastIndex];
                if (str_contains($lastP, 'لليوم فقط') || str_contains($lastP, 'Just for today')) {
                    $thoughtForTheDay = $lastP;
                    $contentParagraphs = array_slice($paragraphs, 2, $lastIndex - 2);
                } else {
                    $contentParagraphs = array_slice($paragraphs, 2);
                }
            }
        }

        return [
            'date' => $date->format('Y-m-d'),
            'page_date' => $pageDate ?: $date->translatedFormat('j F'),
            'title' => $title,
            'quote' => $quote,
            'quote_source' => $quoteSource,
            'content' => $contentParagraphs,
            'thought_for_the_day' => $thoughtForTheDay,
            'content_html' => $bodyHtml,
        ];
    }
}
