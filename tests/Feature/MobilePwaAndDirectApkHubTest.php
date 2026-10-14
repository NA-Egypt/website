<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MobilePwaAndDirectApkHubTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LocalizationRedirect::class,
        ]);
    }

    /**
     * Test direct APK download endpoint streams or redirects to upstream release.
     */
    public function test_direct_apk_download_route_streams_package(): void
    {
        Http::fake([
            'https://github.com/NA-Egypt/*' => Http::response('FAKE_APK_BINARY_CONTENT', 200, [
                'Content-Type' => 'application/vnd.android.package-archive',
                'Content-Length' => '23',
            ]),
        ]);

        $response = $this->get(route('apk.direct_download'));

        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));
        if ($response->getStatusCode() === 200) {
            $response->assertHeader('Content-Type', 'application/vnd.android.package-archive');
            $response->assertHeader('Content-Disposition', 'attachment; filename="naegypt-v1.2.0-hope.apk"');
        }
    }

    /**
     * Test dedicated /app page renders direct APK hub and PWA sections in Arabic.
     */
    public function test_dedicated_app_page_renders_hub_in_arabic(): void
    {
        app()->setLocale('ar');
        $response = $this->get(route('frontend.app'));

        $response->assertStatus(200);
        $response->assertSee('naegypt-v1.2.0-hope.apk');
        $response->assertSee('a3f89b6c429d71e289e631df8435d10d65b1c90538743128c11e5f80b2d69941');
        $response->assertSee('28.4 MB');
        $response->assertSee('دليل التثبيت الآمن في 3 خطوات بسيطة');
        $response->assertSee('apk-download-qr.svg');
    }

    /**
     * Test dedicated /app page renders in English.
     */
    public function test_dedicated_app_page_renders_hub_in_english(): void
    {
        app()->setLocale('en');
        $response = $this->get(route('frontend.app'));

        $response->assertStatus(200);
        $response->assertSee('naegypt-v1.2.0-hope.apk');
        $response->assertSee('NA Fellowship Android App');
        $response->assertSee('Safe 3-Step Installation Guide');
    }

    /**
     * Test Web App Manifest file is valid JSON with Arabic RTL attributes and NA icons.
     */
    public function test_pwa_manifest_is_configured_properly(): void
    {
        $manifestPath = public_path('manifest.json');
        $this->assertFileExists($manifestPath);

        $json = json_decode(file_get_contents($manifestPath), true);
        $this->assertIsArray($json);

        $this->assertEquals('زمالة المدمنين المجهولين - مصر', $json['name']);
        $this->assertEquals('NA Egypt', $json['short_name']);
        $this->assertEquals('#1B2A4A', $json['theme_color']);
        $this->assertEquals('#1B2A4A', $json['background_color']);
        $this->assertEquals('rtl', $json['dir']);
        $this->assertEquals('ar', $json['lang']);
        $this->assertEquals('standalone', $json['display']);

        $iconPaths = array_column($json['icons'], 'src');
        $this->assertContains('/assets/images/icons/icon-192.png', $iconPaths);
        $this->assertContains('/assets/images/icons/icon-512.png', $iconPaths);
        $this->assertContains('/assets/images/icons/icon-maskable.png', $iconPaths);
    }

    /**
     * Test Service Worker and Offline Emergency Fallback exist and are accessible.
     */
    public function test_service_worker_and_offline_fallback_exist(): void
    {
        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath);
        $swContent = file_get_contents($swPath);
        $this->assertStringContainsString('naegypt-v1.2', $swContent);
        $this->assertStringContainsString('/offline.html', $swContent);

        $offlinePath = public_path('offline.html');
        $this->assertFileExists($offlinePath);
        $offlineContent = file_get_contents($offlinePath);
        $this->assertStringContainsString('01060933888', $offlineContent);
        $this->assertStringContainsString('01006979198', $offlineContent);
        $this->assertStringContainsString('لليوم فقط', $offlineContent);
    }

    /**
     * Test HTML Layout embeds PWA meta tags, manifest link, Service Worker script, and install banner.
     */
    public function test_layout_embeds_pwa_meta_tags_and_components(): void
    {
        app()->setLocale('ar');
        $response = $this->get(route('frontend.app'));

        $response->assertStatus(200);
        $response->assertSee('<link rel="manifest" href="/manifest.json">', false);
        $response->assertSee('<meta name="theme-color" content="#1B2A4A">', false);
        $response->assertSee('<link rel="apple-touch-icon"', false);
        $response->assertSee('naPwaInstallBanner');
        $response->assertSee('naIosPwaModal');
        $response->assertSee("navigator.serviceWorker.register('/sw.js')", false);
    }
}
