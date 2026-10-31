<?php

namespace Tests\Unit\Services;

use App\Services\Search\ArabicNormalizer;
use PHPUnit\Framework\TestCase;

class ArabicNormalizerTest extends TestCase
{
    /**
     * Test acceptance criteria: normalize('مَجْمُوعَة المَعَادِي') produces 'مجموعه المعادي'.
     */
    public function test_normalizes_tashkeel_and_ta_marbuta_acceptance_case(): void
    {
        $input = 'مَجْمُوعَة المَعَادِي';
        $expected = 'مجموعه المعادي';

        $this->assertSame($expected, ArabicNormalizer::normalize($input));
    }

    /**
     * Test stripping various Arabic diacritics and tashkeel.
     */
    public function test_strips_all_diacritics(): void
    {
        // Fatha, Damma, Kasra, Sukun, Shadda, Tanween Fath/Damm/Kasr, Superscript Alef
        $input = 'كِتَابٌ جَدِيدٌ، قَرَأْتُهُ فِي الْمَدْرَسَةِ!';
        $expected = 'كتاب جديد، قراته في المدرسه!';

        $this->assertSame($expected, ArabicNormalizer::normalize($input));
    }

    /**
     * Test stripping Tatweel / Kashida (\x{0640}).
     */
    public function test_strips_tatweel(): void
    {
        $input = 'مــــعـــــادي';
        $expected = 'معادي';

        $this->assertSame($expected, ArabicNormalizer::normalize($input));
    }

    /**
     * Test normalizing Alef variants [أإآٱ] to bare Alef [ا].
     */
    public function test_normalizes_alef_variants(): void
    {
        $input = 'أحمد إبراهيم آمنة ٱجتماع';
        $expected = 'احمد ابراهيم امنه اجتماع';

        $this->assertSame($expected, ArabicNormalizer::normalize($input));
    }

    /**
     * Test normalizing Hamza variants [ؤئ] to isolated Hamza [ء].
     */
    public function test_normalizes_hamza_variants(): void
    {
        $input = 'مسؤولية وشاطئ وبؤس';
        $expected = 'مسءوليه وشاطء وبءس';

        $this->assertSame($expected, ArabicNormalizer::normalize($input));
    }

    /**
     * Test normalizing Ta Marbuta (ة) to Ha (ه).
     */
    public function test_normalizes_ta_marbuta(): void
    {
        $input = 'مدينة القاهرة الجديدة';
        $expected = 'مدينه القاهره الجديده';

        $this->assertSame($expected, ArabicNormalizer::normalize($input));
    }

    /**
     * Test normalizing Alif Maqsura (ى) to Ya (ي).
     */
    public function test_normalizes_alif_maqsura(): void
    {
        $input = 'مستشفى مصطفى على الهدى';
        $expected = 'مستشفي مصطفي علي الهدي';

        $this->assertSame($expected, ArabicNormalizer::normalize($input));
    }

    /**
     * Test normalizing Eastern Arabic-Indic and Persian digits to Western digits.
     */
    public function test_normalizes_eastern_and_persian_digits(): void
    {
        $eastern = 'رقم الهاتف ٠١٢٣٤٥٦٧٨٩';
        $persian = 'کد پستی ۰۱۲۳۴۵۶۷۸۹';

        $this->assertSame('رقم الهاتف 0123456789', ArabicNormalizer::normalize($eastern));
        $this->assertSame('کد پستی 0123456789', ArabicNormalizer::normalize($persian));
    }

    /**
     * Test normalizing mixed English text to lowercase and trimming extra whitespace.
     */
    public function test_normalizes_mixed_english_and_whitespace(): void
    {
        $input = '  NA Egypt   Meeting  الـمعادي 2026   ';
        $expected = 'na egypt meeting المعادي 2026';

        $this->assertSame($expected, ArabicNormalizer::normalize($input));
    }

    /**
     * Test handling null and empty string inputs safely.
     */
    public function test_handles_null_and_empty_strings(): void
    {
        $this->assertSame('', ArabicNormalizer::normalize(null));
        $this->assertSame('', ArabicNormalizer::normalize(''));
        $this->assertSame('', ArabicNormalizer::normalize('   '));
    }

    /**
     * Test tokenize method with word splitting, punctuation trimming, and 'ال' expansion.
     */
    public function test_tokenize_extracts_normalized_unique_tokens(): void
    {
        $input = 'اجتماع مجموعة المعادي (المعادي - القاهرة!)';
        $tokens = ArabicNormalizer::tokenize($input);

        // Should include normalized words, stripped 'ال' for 'المعادي' ('معادي') and 'القاهرة' ('قاهره')
        $this->assertContains('اجتماع', $tokens);
        $this->assertContains('مجموعه', $tokens);
        $this->assertContains('المعادي', $tokens);
        $this->assertContains('معادي', $tokens);
        $this->assertContains('القاهره', $tokens);
        $this->assertContains('قاهره', $tokens);

        // Ensure tokens are deduplicated
        $this->assertSame(count($tokens), count(array_unique($tokens)));
    }

    /**
     * Test generateSearchVariants for terms with and without 'ال'.
     */
    public function test_generate_search_variants(): void
    {
        $withAlVariants = ArabicNormalizer::generateSearchVariants('المعادي');
        $this->assertContains('المعادي', $withAlVariants);
        $this->assertContains('معادي', $withAlVariants);

        $withoutAlVariants = ArabicNormalizer::generateSearchVariants('معادي');
        $this->assertContains('معادي', $withoutAlVariants);
        $this->assertContains('المعادي', $withoutAlVariants);

        $this->assertSame([], ArabicNormalizer::generateSearchVariants(''));
    }

    /**
     * Test acceptance criteria for matches():
     * - Query 'معادي' matches 'المعادي'
     * - Query 'احمد' matches 'أحمد'
     */
    public function test_matches_acceptance_criteria(): void
    {
        // Query 'معادي' matches 'المعادي'
        $this->assertTrue(ArabicNormalizer::matches('معادي', 'المعادي'));
        $this->assertTrue(ArabicNormalizer::matches('المعادي', 'معادي'));
        $this->assertTrue(ArabicNormalizer::matches('معادي', 'مجموعة المعادي لخدمات الزمالة'));

        // Query 'احمد' matches 'أحمد'
        $this->assertTrue(ArabicNormalizer::matches('احمد', 'أحمد'));
        $this->assertTrue(ArabicNormalizer::matches('أحمد', 'احمد'));
        $this->assertTrue(ArabicNormalizer::matches('احمد', 'سكرتير اللجنة: أحمد إبراهيم'));

        // Diacritics matching
        $this->assertTrue(ArabicNormalizer::matches('مجموعه', 'مَجْمُوعَة'));
        $this->assertTrue(ArabicNormalizer::matches('مَجْمُوعَة', 'مجموعه'));

        // Non-matching query
        $this->assertFalse(ArabicNormalizer::matches('اسكندرية', 'القاهرة والمعادي'));
        $this->assertFalse(ArabicNormalizer::matches('', 'المعادي'));
        $this->assertFalse(ArabicNormalizer::matches('المعادي', ''));
    }
}
