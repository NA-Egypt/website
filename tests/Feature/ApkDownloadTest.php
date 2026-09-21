<?php

namespace Tests\Feature;

use App\Mail\ApkDownloadLinkMail;
use App\Models\ApkDownloadRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApkDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }

    /**
     * Test homepage renders APK hero pill button and revamped request modal with correct elements and no prohibited words.
     */
    public function test_homepage_renders_apk_hero_pill_and_modal(): void
    {
        // Arabic view check
        app()->setLocale('ar');
        $responseAr = $this->get(route('frontend.home'));
        $responseAr->assertStatus(200);
        $responseAr->assertSee('btn-apk-hero');
        $responseAr->assertSee('apkDownloadModal');
        $responseAr->assertSee('apk-modal-content');
        $responseAr->assertSee('apkFormView');
        $responseAr->assertSee('apkSuccessView');
        $responseAr->assertSee('apkRequestForm');
        $responseAr->assertSee('your.email@example.com');
        $responseAr->assertSee('v1.2.0-Hope');
        // Verify absence of prohibited words in APK modal
        $responseAr->assertDontSee('البريد الإلكتروني الرسمي');
        $responseAr->assertDontSee('رابط التحميل السري');
        $responseAr->assertDontSee('السري والمباشر');

        // English view check
        app()->setLocale('en');
        $responseEn = $this->get(route('frontend.home'));
        $responseEn->assertStatus(200);
        $responseEn->assertSee('btn-apk-hero');
        $responseEn->assertSee('apkDownloadModal');
        $responseEn->assertSee('apk-modal-content');
        $responseEn->assertSee('v1.2.0-Hope');
        // Verify absence of prohibited English words in APK modal context
        $responseEn->assertDontSee('Official Email Address');
        $responseEn->assertDontSee('Secret Download Link');
    }

    /**
     * Test request link rejects invalid email format.
     */
    public function test_request_link_validation_rejects_invalid_email(): void
    {
        $response = $this->postJson(route('apk.request_link'), [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test request link succeeds with any valid email (e.g. Gmail, visitor, etc.).
     */
    public function test_request_link_succeeds_with_any_valid_email(): void
    {
        Mail::fake();

        $email = 'visitor@gmail.com';

        $response = $this->postJson(route('apk.request_link'), [
            'email' => $email,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('apk_download_requests', [
            'email' => $email,
        ]);

        Mail::assertSent(ApkDownloadLinkMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email) && !empty($mail->downloadUrl);
        });
    }

    /**
     * Test request link verifies Cloudflare Turnstile token when verification is enabled.
     */
    public function test_request_link_fails_when_turnstile_rejects_token(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        $response = $this->withHeader('X-Test-Turnstile-Verification', '1')
            ->postJson(route('apk.request_link'), [
                'email' => 'servant@naegypt.org',
                'cf-turnstile-response' => 'invalid-token',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cf-turnstile-response']);
    }

    /**
     * Test request link succeeds with valid Turnstile token.
     */
    public function test_request_link_passes_with_verified_turnstile(): void
    {
        Mail::fake();

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'challenge_ts' => now()->toIso8601String(),
                'hostname' => 'naegypt.org',
            ], 200),
        ]);

        $response = $this->withHeader('X-Test-Turnstile-Verification', '1')
            ->postJson(route('apk.request_link'), [
                'email' => 'servant@naegypt.org',
                'cf-turnstile-response' => 'valid-turnstile-token',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test request link succeeds with official @naegypt.org email.
     */
    public function test_request_link_succeeds_with_valid_naegypt_email(): void
    {
        Mail::fake();

        $email = 'it.chair@naegypt.org';

        $response = $this->postJson(route('apk.request_link'), [
            'email' => $email,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('apk_download_requests', [
            'email' => $email,
        ]);

        Mail::assertSent(ApkDownloadLinkMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email) && !empty($mail->downloadUrl);
        });
    }

    /**
     * Test download aborts 403 when token does not exist.
     */
    public function test_download_fails_when_token_not_found(): void
    {
        $response = $this->get(route('apk.download', ['token' => 'nonexistent_token_123']));
        $response->assertStatus(403);
    }

    /**
     * Test download aborts 403 when token has expired.
     */
    public function test_download_fails_when_token_is_expired(): void
    {
        $token = Str::random(64);
        ApkDownloadRequest::create([
            'email' => 'servant@naegypt.org',
            'token' => $token,
            'expires_at' => now()->subHours(2),
        ]);

        $response = $this->get(route('apk.download', ['token' => $token]));
        $response->assertStatus(403);
    }

    /**
     * Test download proxies binary stream and increments download count.
     */
    public function test_download_streams_file_and_increments_count(): void
    {
        $token = Str::random(64);
        $apkRequest = ApkDownloadRequest::create([
            'email' => 'trusted.servant@naegypt.org',
            'token' => $token,
            'expires_at' => now()->addHours(24),
            'download_count' => 0,
        ]);

        $fakeApkContent = 'FAKE_APK_BINARY_CONTENT_12345';

        Http::fake([
            config('services.apk.release_url') => Http::response($fakeApkContent, 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => strlen($fakeApkContent),
            ]),
        ]);

        $response = $this->get(route('apk.download', ['token' => $token]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.android.package-archive');
        $response->assertHeader('Content-Disposition', 'attachment; filename="' . config('services.apk.filename', 'na-egypt-1.2.0.apk') . '"');

        $apkRequest->refresh();
        $this->assertEquals(1, $apkRequest->download_count);
        $this->assertNotNull($apkRequest->last_downloaded_at);
    }
}
