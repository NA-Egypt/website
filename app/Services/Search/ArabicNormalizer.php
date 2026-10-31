<?php

namespace App\Services\Search;

class ArabicNormalizer
{
    /**
     * Map of Eastern Arabic-Indic and Persian digits to Western Arabic digits.
     *
     * @var array<string, string>
     */
    protected const DIGIT_MAP = [
        // Eastern Arabic-Indic digits
        '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
        '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        // Persian digits
        '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
        '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
    ];

    /**
     * Normalize Arabic and mixed English text for search indexation and query matching.
     *
     * Rules applied:
     * 1. Strips all Arabic diacritics / tashkeel (Fatha, Damma, Kasra, Sukun, Shadda, Tanween, etc.).
     * 2. Strips Tatweel / Kashida (\x{0640}).
     * 3. Normalizes Alef variants: [أإآٱ] -> ا.
     * 4. Normalizes Hamza variants: [ؤئ] -> ء.
     * 5. Normalizes Ta Marbuta: ة -> ه.
     * 6. Normalizes Alif Maqsura: ى -> ي.
     * 7. Normalizes Eastern Arabic-Indic and Persian digits -> 0-9.
     * 8. Normalizes mixed English queries: lowercase and trimmed whitespace.
     *
     * @param string|null $text
     * @return string
     */
    public static function normalize(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        mb_regex_encoding('UTF-8');

        // 1. Strip Arabic diacritics / Tashkeel [\x{064B}-\x{065F}\x{0670}]
        // Covers Tanween (064B-064D), Harakat (064E-0650), Shadda (0651), Sukun (0652),
        // Maddah/Hamza diacritics (0653-065F), and superscript Alef (0670)
        $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text) ?? $text;

        // 2. Strip Tatweel / Kashida (\x{0640})
        $text = preg_replace('/\x{0640}/u', '', $text) ?? $text;

        // 3. Normalize Alef variants: [أإآٱ] -> ا
        $text = preg_replace('/[أإآٱ]/u', 'ا', $text) ?? $text;

        // 4. Normalize Hamza variants: [ؤئ] -> ء
        $text = preg_replace('/[ؤئ]/u', 'ء', $text) ?? $text;

        // 5. Normalize Ta Marbuta: ة -> ه
        $text = preg_replace('/ة/u', 'ه', $text) ?? $text;

        // 6. Normalize Alif Maqsura: ى -> ي
        $text = preg_replace('/ى/u', 'ي', $text) ?? $text;

        // 7. Normalize Eastern Arabic-Indic and Persian digits to standard Western digits
        $text = strtr($text, self::DIGIT_MAP);

        // 8. Normalize mixed English characters (lowercase)
        $text = mb_strtolower($text, 'UTF-8');

        // 9. Normalize multiple whitespace and trim
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    /**
     * Tokenize text into normalized words for database full-text matching.
     * Splits on punctuation and whitespace, expands words with/without the 'ال' prefix,
     * filters out empty entries, and returns deduplicated tokens.
     *
     * @param string $text
     * @return array<int, string>
     */
    public static function tokenize(string $text): array
    {
        $normalized = self::normalize($text);
        if ($normalized === '') {
            return [];
        }

        // Split on non-letter, non-digit unicode characters
        $rawTokens = preg_split('/[^\p{L}\p{N}]+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $tokens = [];
        foreach ($rawTokens as $token) {
            $token = trim($token);
            if ($token === '') {
                continue;
            }

            $tokens[] = $token;

            // If the token starts with the Arabic definite article "ال" (Al-) and has a root of >= 2 chars
            if (mb_strlen($token) > 3 && mb_substr($token, 0, 2) === 'ال') {
                $stripped = mb_substr($token, 2);
                if (mb_strlen($stripped) >= 2) {
                    $tokens[] = $stripped;
                }
            }
        }

        return array_values(array_unique($tokens));
    }

    /**
     * Generate search variants for a single term (e.g., both with and without "ال").
     *
     * @param string $term
     * @return array<int, string>
     */
    public static function generateSearchVariants(string $term): array
    {
        $normalized = self::normalize($term);
        if ($normalized === '') {
            return [];
        }

        $variants = [$normalized];

        if (mb_strlen($normalized) > 3 && mb_substr($normalized, 0, 2) === 'ال') {
            $stripped = mb_substr($normalized, 2);
            if (mb_strlen($stripped) >= 2) {
                $variants[] = $stripped;
            }
        } elseif (mb_strlen($normalized) >= 2 && mb_substr($normalized, 0, 2) !== 'ال') {
            $variants[] = 'ال' . $normalized;
        }

        return array_values(array_unique($variants));
    }

    /**
     * Determine whether a search query matches the target text under Arabic normalization rules.
     * Supports prefix variation (e.g. 'معادي' matches 'المعادي', 'احمد' matches 'أحمد').
     *
     * @param string $query
     * @param string $target
     * @return bool
     */
    public static function matches(string $query, string $target): bool
    {
        $normQuery = self::normalize($query);
        $normTarget = self::normalize($target);

        if ($normQuery === '' || $normTarget === '') {
            return false;
        }

        // Direct normalized substring match
        if (mb_strpos($normTarget, $normQuery) !== false) {
            return true;
        }

        // Search variants match (e.g. with or without "ال")
        $queryVariants = self::generateSearchVariants($normQuery);
        foreach ($queryVariants as $variant) {
            if (mb_strpos($normTarget, $variant) !== false) {
                return true;
            }
        }

        // Token-based matching: verify if any query token exists in target
        $queryTokens = self::tokenize($normQuery);
        $targetTokens = self::tokenize($normTarget);
        $targetTokensSet = array_flip($targetTokens);

        foreach ($queryTokens as $qToken) {
            if (isset($targetTokensSet[$qToken])) {
                return true;
            }
            foreach (self::generateSearchVariants($qToken) as $variant) {
                if (isset($targetTokensSet[$variant])) {
                    return true;
                }
            }
        }

        return false;
    }
}

