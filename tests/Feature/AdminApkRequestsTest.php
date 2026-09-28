<?php

namespace Tests\Feature;

use App\Mail\ApkDownloadLinkMail;
use App\Models\ApkDownloadRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminApkRequestsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@example.com',
        ]);
        $this->superAdmin->assignRole('super admin');

        $this->regularUser = User::factory()->create([
            'email' => 'member@example.com',
        ]);
    }

    /**
     * Unauthenticated guest is redirected to home.
     */
    public function test_guest_cannot_access_apk_requests_index(): void
    {
        $response = $this->get(route('admin.apk_requests.index'));
        $response->assertRedirect('/');
    }

    /**
     * Non-super-admin authenticated user receives 403.
     */
    public function test_non_super_admin_cannot_access_apk_requests_index(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.apk_requests.index'));
        $response->assertStatus(403);
    }

    /**
     * Super Admin can view index page with KPI stats and requests listing.
     */
    public function test_super_admin_can_view_index_page_and_kpis(): void
    {
        ApkDownloadRequest::create([
            'email' => 'user1@example.com',
            'token' => Str::random(64),
            'ip_address' => '1.2.3.4',
            'expires_at' => now()->addHours(24),
            'download_count' => 2,
            'last_downloaded_at' => now(),
        ]);

        ApkDownloadRequest::create([
            'email' => 'user2@example.com',
            'token' => Str::random(64),
            'ip_address' => '5.6.7.8',
            'expires_at' => now()->subHours(1),
            'download_count' => 0,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.apk_requests.index'));

        $response->assertStatus(200);
        $response->assertSee('user1@example.com');
        $response->assertSee('user2@example.com');
        $response->assertSee('1.2.3.4');
        $response->assertSee('5.6.7.8');
    }

    /**
     * Search filter filters requests by email or IP.
     */
    public function test_super_admin_can_filter_by_search_query(): void
    {
        ApkDownloadRequest::create([
            'email' => 'unique-search@test.com',
            'token' => Str::random(64),
            'ip_address' => '192.168.1.100',
            'expires_at' => now()->addHours(24),
        ]);

        ApkDownloadRequest::create([
            'email' => 'other@test.com',
            'token' => Str::random(64),
            'ip_address' => '10.0.0.1',
            'expires_at' => now()->addHours(24),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.apk_requests.index', ['search' => 'unique-search']));
        $response->assertStatus(200);
        $response->assertSee('unique-search@test.com');
        $response->assertDontSee('other@test.com');

        $responseIp = $this->actingAs($this->superAdmin)->get(route('admin.apk_requests.index', ['search' => '10.0.0.1']));
        $responseIp->assertStatus(200);
        $responseIp->assertSee('other@test.com');
        $responseIp->assertDontSee('unique-search@test.com');
    }

    /**
     * Status filter filters by downloaded, pending, and expired.
     */
    public function test_super_admin_can_filter_by_status(): void
    {
        ApkDownloadRequest::create([
            'email' => 'downloaded@test.com',
            'token' => Str::random(64),
            'expires_at' => now()->addHours(12),
            'download_count' => 1,
        ]);

        ApkDownloadRequest::create([
            'email' => 'pending@test.com',
            'token' => Str::random(64),
            'expires_at' => now()->addHours(12),
            'download_count' => 0,
        ]);

        ApkDownloadRequest::create([
            'email' => 'expired@test.com',
            'token' => Str::random(64),
            'expires_at' => now()->subDay(),
            'download_count' => 0,
        ]);

        // Filter downloaded
        $respDownloaded = $this->actingAs($this->superAdmin)->get(route('admin.apk_requests.index', ['status' => 'downloaded']));
        $respDownloaded->assertSee('downloaded@test.com');
        $respDownloaded->assertDontSee('pending@test.com');
        $respDownloaded->assertDontSee('expired@test.com');

        // Filter pending
        $respPending = $this->actingAs($this->superAdmin)->get(route('admin.apk_requests.index', ['status' => 'pending']));
        $respPending->assertSee('pending@test.com');
        $respPending->assertDontSee('downloaded@test.com');
        $respPending->assertDontSee('expired@test.com');

        // Filter expired
        $respExpired = $this->actingAs($this->superAdmin)->get(route('admin.apk_requests.index', ['status' => 'expired']));
        $respExpired->assertSee('expired@test.com');
        $respExpired->assertDontSee('downloaded@test.com');
        $respExpired->assertDontSee('pending@test.com');
    }

    /**
     * Resend link regenerates token, extends expiry, and dispatches email.
     */
    public function test_super_admin_can_resend_apk_download_link(): void
    {
        Mail::fake();

        $originalToken = 'old_token_' . Str::random(54);
        $request = ApkDownloadRequest::create([
            'email' => 'requester@test.com',
            'token' => $originalToken,
            'expires_at' => now()->subHour(), // expired
            'download_count' => 0,
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.apk_requests.resend', $request));

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $request->refresh();
        $this->assertNotEquals($originalToken, $request->token);
        $this->assertTrue($request->expires_at->isFuture());

        Mail::assertSent(ApkDownloadLinkMail::class, function ($mail) use ($request) {
            return $mail->apkRequest->id === $request->id && $mail->hasTo('requester@test.com');
        });
    }

    /**
     * Super Admin can delete a single download request.
     */
    public function test_super_admin_can_delete_single_request(): void
    {
        $request = ApkDownloadRequest::create([
            'email' => 'delete-me@test.com',
            'token' => Str::random(64),
            'expires_at' => now()->addHours(24),
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.apk_requests.destroy', $request));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('apk_download_requests', [
            'id' => $request->id,
        ]);
    }

    /**
     * Super Admin can bulk delete selected requests.
     */
    public function test_super_admin_can_bulk_delete_requests(): void
    {
        $r1 = ApkDownloadRequest::create([
            'email' => 'bulk1@test.com',
            'token' => Str::random(64),
            'expires_at' => now()->addHours(24),
        ]);
        $r2 = ApkDownloadRequest::create([
            'email' => 'bulk2@test.com',
            'token' => Str::random(64),
            'expires_at' => now()->addHours(24),
        ]);
        $r3 = ApkDownloadRequest::create([
            'email' => 'keep@test.com',
            'token' => Str::random(64),
            'expires_at' => now()->addHours(24),
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.apk_requests.bulk_destroy'), [
            'ids' => [$r1->id, $r2->id],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('apk_download_requests', ['id' => $r1->id]);
        $this->assertDatabaseMissing('apk_download_requests', ['id' => $r2->id]);
        $this->assertDatabaseHas('apk_download_requests', ['id' => $r3->id]);
    }

    /**
     * Super Admin can export requests to CSV.
     */
    public function test_super_admin_can_export_csv(): void
    {
        ApkDownloadRequest::create([
            'email' => 'export-test@test.com',
            'token' => Str::random(64),
            'ip_address' => '8.8.8.8',
            'expires_at' => now()->addHours(24),
            'download_count' => 3,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.apk_requests.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('attachment; filename=', $response->headers->get('content-disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('export-test@test.com', $content);
        $this->assertStringContainsString('8.8.8.8', $content);
        $this->assertStringContainsString('Downloaded', $content);
    }
}
