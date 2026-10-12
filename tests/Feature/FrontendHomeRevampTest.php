<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FrontendHomeRevampTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the Arabic homepage loads with all 6 modular sections and emergency action bar.
     */
    public function test_homepage_arabic_loads_successfully_with_all_sections(): void
    {
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LocalizationRedirect::class,
        ]);

        app()->setLocale('ar');
        $response = $this->get(route('frontend.home'));

        $response->assertStatus(200);

        // Section 1: Hero & Tri-Path Audience Segmenter
        $response->assertSee('أهلاً بك في زمالة المدمنين المجهولين - مصر');
        $response->assertSee('أنا أبحث عن مساعدة لنفسي');
        $response->assertSee('أبحث عن مساعدة لشخص آخر');
        $response->assertSee('أنا عضو بالزمالة');

        // Section 2: Meeting Finder
        $response->assertSee('ابحث عن اجتماع تعافي الآن');
        $response->assertSee('meeting-finder-section');

        // Section 4: Clean-Time Calculator & Keytags
        $response->assertSee('احسب أيام تعافيك واحتفل برحلتك');
        $response->assertSee('cleantime-section');
        $response->assertSee('kt-welcome');
        $response->assertSee('kt-30d');
        $response->assertSee('kt-60d');
        $response->assertSee('kt-90d');
        $response->assertSee('kt-6m');
        $response->assertSee('kt-9m');
        $response->assertSee('kt-1y');
        $response->assertSee('kt-18m');
        $response->assertSee('kt-multi');

        // Section 5: News & Announcements
        $response->assertSee('أخبار الزمالة، المؤتمرات، وورش العمل');

        // Section 6: Comprehensive Sanctuary Footer
        $response->assertSee('أدبيات التعافي');
        $response->assertSee('لجان وهيكل الخدمة');
        $response->assertSee('التقليد الثاني عشر');

        // Persistent Emergency Action Bar
        $response->assertSee('naEmergencyActionBar');
        $response->assertSee('+20 100 697 9198');
        $response->assertSee('+20 106 093 3888');
        $response->assertSee('+20 150 388 4411');

        // Verify elimination of repeated excerpts
        $response->assertDontSee('عندما نكتشف في نهاية الطريق');
    }

    /**
     * Test the English homepage loads with all sections properly localized.
     */
    public function test_homepage_english_loads_successfully_with_localization(): void
    {
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LocalizationRedirect::class,
        ]);

        app()->setLocale('en');
        $response = $this->get(route('frontend.home'));

        $response->assertStatus(200);

        // Section 1: Hero & Tri-Path Segmenter
        $response->assertSee('Welcome to Narcotics Anonymous - Egypt');
        $response->assertSee('I Am Looking for Help for Myself');
        $response->assertSee('Looking for Help for Someone Else');
        $response->assertSee('I Am a Fellowship Member');

        // Section 2: Meeting Finder
        $response->assertSee('Find a Recovery Meeting Now');

        // Section 4: Clean-Time Calculator
        $response->assertSee('Calculate Your Clean Time & Celebrate Your Journey');

        // Section 5: News
        $response->assertSee('Fellowship News, Conventions & Workshops');

        // Section 6: Footer
        $response->assertSee('Recovery Literature');
        $response->assertSee('Tradition Twelve');

        // Persistent Emergency Action Bar
        $response->assertSee('naEmergencyActionBar');
        $response->assertSee('+20 100 697 9198');
    }

    /**
     * Test the interactive Meeting Finder component elements, filters, dataset, and compassionate fallback.
     */
    public function test_homepage_meeting_finder_interactive_components_and_dataset(): void
    {
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LocalizationRedirect::class,
        ]);

        app()->setLocale('ar');
        $response = $this->get(route('frontend.home'));

        $response->assertStatus(200);

        // View data assertions
        $response->assertViewHas('locations');
        $response->assertViewHas('allMeetingsJson');

        // Smart Upcoming Highlight Card
        $response->assertSee('smartUpcomingCardWrap');
        $response->assertSee('أقرب اجتماع يبدأ قريباً');
        $response->assertSee('upcomingCountdownBadge');

        // Location Search Autocomplete & Quick Chips
        $response->assertSee('meetingFinderLocationInput');
        $response->assertSee('meetingFinderLocationDropdown');
        $response->assertSee('القاهرة');
        $response->assertSee('الجيزة');
        $response->assertSee('الإسكندرية');
        $response->assertSee('المعادي');
        $response->assertSee('مدينة نصر');
        $response->assertSee('المهندسين');
        $response->assertSee('مصر الجديدة');
        $response->assertSee('مدينة 6 أكتوبر');
        $response->assertSee('المنصورة');
        $response->assertSee('طنطا');
        $response->assertSee('أسيوط');

        // Day of Week & Time Slot Filters
        $response->assertSee('meetingFinderDaySelect');
        $response->assertSee('meetingFinderTimeSlotSelect');
        $response->assertSee('فترة الاجتماع');
        $response->assertSee('صباحاً');
        $response->assertSee('ظهراً');
        $response->assertSee('مساءً');
        $response->assertSee('ليلاً');

        // Format Toggle
        $response->assertSee('fmtBtnAll');
        $response->assertSee('fmtBtnInPerson');
        $response->assertSee('fmtBtnOnline');

        // Geolocation Proximity Sort
        $response->assertSee('meetingFinderGeoBtn');
        $response->assertSee('استخدم موقعي الحالي');

        // Compassionate Fallback
        $response->assertSee('noMeetingFoundMsg');
        $response->assertSee('لم يتم العثور على اجتماعات في هذا النطاق، لكن هناك اجتماعات أونلاين متاحة على مدار الساعة');
        $response->assertSee('عرض اجتماعات أونلاين الآن');
    }
}
