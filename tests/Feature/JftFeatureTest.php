<?php

namespace Tests\Feature;

use App\Services\JftService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class JftFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_jft_service_returns_arabic_reading_by_default(): void
    {
        $service = app(JftService::class);
        $reading = $service->getReading('2026-01-10', 'ar');

        $this->assertEquals('2026-01-10', $reading['date']);
        $this->assertEquals('الامتنان', $reading['title']);
        $this->assertEquals('10 يناير', $reading['page_date']);
        $this->assertNotEmpty($reading['quote']);
        $this->assertNotEmpty($reading['quote_source']);
        $this->assertNotEmpty($reading['thought_for_the_day']);
        $this->assertNotEmpty($reading['content']);
    }

    public function test_jft_service_returns_english_reading_from_jft_en(): void
    {
        $service = app(JftService::class);
        $reading = $service->getReading('2026-01-10', 'en');

        $this->assertEquals('2026-01-10', $reading['date']);
        $this->assertEquals('Gratitude', $reading['title']);
        $this->assertEquals('January 10', $reading['page_date']);
        $this->assertStringContainsString('grateful', $reading['quote']);
        $this->assertEquals('IP No. 21, The Loner', $reading['quote_source']);
        $this->assertStringContainsString('Just for today', $reading['thought_for_the_day']);
        $this->assertNotEmpty($reading['content']);
    }

    public function test_jft_service_reads_different_dates_properly(): void
    {
        $service = app(JftService::class);
        $reading = $service->getReading('2026-05-15', 'en');
        $this->assertEquals('Fear of the Fourth Step', $reading['title']);
        $this->assertEquals('May 15', $reading['page_date']);
    }

    public function test_jft_api_validates_locale(): void
    {
        $response = $this->getJson('/api/v1/jft?date=2026-01-10&locale=invalid');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['locale']);
    }

    public function test_jft_api_accepts_locale_en(): void
    {
        $response = $this->getJson('/api/v1/jft?date=2026-01-10&locale=en');
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'date' => '2026-01-10',
                    'title' => 'Gratitude',
                    'page_date' => 'January 10',
                ]
            ]);
    }

    public function test_home_api_supports_english_locale(): void
    {
        $response = $this->getJson('/api/v1/home?date=2026-01-10&locale=en');
        $response->assertStatus(200);

        $jft = $response->json('data.jft');
        $this->assertNotNull($jft);
        $this->assertEquals('Gratitude', $jft['title']);
        $this->assertEquals('January 10', $jft['page_date']);
    }

    public function test_jft_component_renders_correct_direction_per_locale(): void
    {
        // Test English rendering
        app()->setLocale('en');
        $enHtml = view('components.frontend.jft-card', [
            'jftReading' => app(JftService::class)->getReading('2026-01-10', 'en'),
        ])->render();

        $this->assertStringContainsString('id="jftReadingContainer" class="jft-reading-card-surface position-relative" dir="ltr"', $enHtml);
        $this->assertStringContainsString('id="jftSection"', $enHtml);
        $this->assertStringContainsString('dir="ltr"', $enHtml);

        // Test Arabic rendering
        app()->setLocale('ar');
        $arHtml = view('components.frontend.jft-card', [
            'jftReading' => app(JftService::class)->getReading('2026-01-10', 'ar'),
        ])->render();

        $this->assertStringContainsString('id="jftReadingContainer" class="jft-reading-card-surface position-relative" dir="rtl"', $arHtml);
    }
}
