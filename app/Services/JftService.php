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
     * @param string|null $locale 'ar' or 'en' (defaults to current app locale)
     * @return array
     */
    public function getReading(?string $dateString = null, ?string $locale = null): array
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

        $locale = $locale ?: app()->getLocale() ?: 'ar';
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        $cacheKey = 'jft_reading_' . $locale . '_' . $date->format('Y_m_d');

        return Cache::remember($cacheKey, 3600, function () use ($date, $locale) {
            return $this->parseReadingFile($date, $locale);
        });
    }

    /**
     * Parse the JFT HTML file for a given Carbon date and locale.
     *
     * @param Carbon $date
     * @param string $locale
     * @return array
     */
    protected function parseReadingFile(Carbon $date, string $locale = 'ar'): array
    {
        $day = $date->day;
        $monthShort = strtolower($date->format('M')); // jan, feb, mar...

        if ($locale === 'en') {
            // Files in public/literature/jft_en are named: DD_Mmm.html (e.g. 01_Jan.html, 10_Jan.html)
            $dayPadded = str_pad((string)$day, 2, '0', STR_PAD_LEFT);
            $monthTitle = ucfirst($monthShort);
            $fileName = "{$dayPadded}_{$monthTitle}.html";
            $filePath = public_path("literature/jft_en/{$fileName}");

            if (!file_exists($filePath) && $monthShort === 'feb' && $day == 29) {
                $filePath = public_path("literature/jft_en/28_Feb.html");
            }

            // Fallback to Arabic reading if English file does not exist
            if (!file_exists($filePath)) {
                $arFileName = "{$day}_{$monthShort}_.html";
                $arFilePath = public_path("literature/jft/{$arFileName}");
                if (!file_exists($arFilePath) && $monthShort === 'feb' && $day == 29) {
                    $arFilePath = public_path("literature/jft/28_feb_.html");
                }
                if (file_exists($arFilePath)) {
                    $rawHtml = file_get_contents($arFilePath);
                    return $this->extractDataFromArabicHtml($rawHtml, $date);
                }
            } else {
                $rawHtml = file_get_contents($filePath);
                return $this->extractDataFromEnglishHtml($rawHtml, $date);
            }
        } else {
            $fileName = "{$day}_{$monthShort}_.html";
            $filePath = public_path("literature/jft/{$fileName}");

            if (!file_exists($filePath) && $monthShort === 'feb' && $day == 29) {
                $filePath = public_path("literature/jft/28_feb_.html");
            }

            if (file_exists($filePath)) {
                $rawHtml = file_get_contents($filePath);
                return $this->extractDataFromArabicHtml($rawHtml, $date);
            }
        }

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

    /**
     * Extract structured fields from the raw English JFT HTML content.
     *
     * @param string $html
     * @param Carbon $date
     * @return array
     */
    protected function extractDataFromEnglishHtml(string $html, Carbon $date): array
    {
        $bodyHtml = '';
        if (preg_match('/<body>(.*?)<\/body>/is', $html, $matches)) {
            $bodyHtml = trim($matches[1]);
        } else {
            $bodyHtml = trim($html);
        }

        // In jft_en HTML files:
        // <h1> contains Date (e.g., "January 10")
        // <h2> contains Title (e.g., "Gratitude")
        $pageDate = '';
        if (preg_match('/<h1>(.*?)<\/h1>/is', $bodyHtml, $matches)) {
            $pageDate = trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        }

        $title = '';
        if (preg_match('/<h2>(.*?)<\/h2>/is', $bodyHtml, $matches)) {
            $title = trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        }

        // Quote & quote source from <blockquote>
        $quote = '';
        $quoteSource = '';
        if (preg_match('/<blockquote>(.*?)<\/blockquote>/is', $bodyHtml, $blockMatches)) {
            $blockContent = $blockMatches[1];
            if (preg_match('/<cite>(.*?)<\/cite>/is', $blockContent, $citeMatches)) {
                $quoteSource = trim(strip_tags(html_entity_decode($citeMatches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            }
            if (preg_match('/<p>(.*?)<\/p>/is', $blockContent, $pMatches)) {
                $quote = trim(strip_tags(html_entity_decode($pMatches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            }
        }

        // Remove blockquote and headings before collecting body paragraphs
        $contentHtmlWithoutBlockquote = preg_replace('/<blockquote>.*?<\/blockquote>/is', '', $bodyHtml);
        $contentHtmlWithoutBlockquote = preg_replace('/<h[1-2]>.*?<\/h[1-2]>/is', '', $contentHtmlWithoutBlockquote);

        $paragraphs = [];
        if (preg_match_all('/<p>(.*?)<\/p>/is', $contentHtmlWithoutBlockquote, $matches)) {
            foreach ($matches[1] as $p) {
                $cleaned = trim(strip_tags(html_entity_decode($p, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                if (!empty($cleaned)) {
                    $paragraphs[] = $cleaned;
                }
            }
        }

        $thoughtForTheDay = '';
        $contentParagraphs = [];

        foreach ($paragraphs as $p) {
            if (preg_match('/^Just for today:?\s*(.*)$/i', $p, $tMatches) || str_starts_with(strtolower($p), 'just for today')) {
                $thoughtForTheDay = $p;
            } else {
                $contentParagraphs[] = $p;
            }
        }

        return [
            'date' => $date->format('Y-m-d'),
            'page_date' => $pageDate ?: $date->locale('en')->isoFormat('MMMM D'),
            'title' => $title,
            'quote' => $quote,
            'quote_source' => $quoteSource,
            'content' => $contentParagraphs,
            'thought_for_the_day' => $thoughtForTheDay,
            'content_html' => $bodyHtml,
        ];
    }

    /**
     * Extract structured fields from the raw Arabic JFT HTML content.
     *
     * @param string $html
     * @param Carbon $date
     * @return array
     */
    protected function extractDataFromArabicHtml(string $html, Carbon $date): array
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

    /**
     * Backwards-compatible alias for HTML extraction.
     *
     * @param string $html
     * @param Carbon $date
     * @return array
     */
    protected function extractDataFromHtml(string $html, Carbon $date): array
    {
        return $this->extractDataFromArabicHtml($html, $date);
    }
}
