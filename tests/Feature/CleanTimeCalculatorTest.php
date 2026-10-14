<?php

namespace Tests\Feature;

use Tests\TestCase;

class CleanTimeCalculatorTest extends TestCase
{
    /**
     * Test the dedicated Clean-Time Calculator Arabic route loads with 200 OK and expected elements.
     */
    public function test_cleantime_page_arabic_renders_successfully(): void
    {
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LocalizationRedirect::class,
        ]);

        app()->setLocale('ar');
        $response = $this->get(route('frontend.cleantime'));

        $response->assertStatus(200);

        // Header and Breadcrumb
        $response->assertSee('حاسبة وقت التعافي');
        $response->assertSee('cleantime-section');

        // All 9 NA Keytag Milestones
        $response->assertSee('kt-welcome');
        $response->assertSee('kt-30d');
        $response->assertSee('kt-60d');
        $response->assertSee('kt-90d');
        $response->assertSee('kt-6m');
        $response->assertSee('kt-9m');
        $response->assertSee('kt-1y');
        $response->assertSee('kt-18m');
        $response->assertSee('kt-multi');

        // Share Card & Canvas elements
        $response->assertSee('naMilestoneCanvas');
        $response->assertSee('naConfettiCanvas');
        $response->assertSee('naShareModal');
        $response->assertSee('حفظ كصورة أو مشاركة');

        // Educational Section
        $response->assertSee('تقليد ميداليات التعافي (Keytags) في الزمالة');
        $response->assertSee('سرية الهوية (Anonymity) في التعافي');
    }

    /**
     * Test the dedicated Clean-Time Calculator English route loads with 200 OK.
     */
    public function test_cleantime_page_english_renders_successfully(): void
    {
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LocalizationRedirect::class,
        ]);

        app()->setLocale('en');
        $response = $this->get(route('frontend.cleantime'));

        $response->assertStatus(200);
        $response->assertSee('Clean-Time Calculator');
        $response->assertSee('The Tradition of NA Recovery Keytags');
        $response->assertSee('Anonymity in Recovery');
    }

    /**
     * Test the navigation bar contains clean-time link in desktop and mobile menus.
     */
    public function test_navbar_contains_cleantime_calculator_link(): void
    {
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LocalizationRedirect::class,
        ]);

        app()->setLocale('ar');
        $response = $this->get(route('frontend.cleantime'));

        $response->assertStatus(200);
        $response->assertSee(route('frontend.cleantime'));
    }
}
