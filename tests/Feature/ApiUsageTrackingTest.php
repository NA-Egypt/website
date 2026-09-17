<?php

namespace Tests\Feature;

use App\Models\ApiDailyStat;
use App\Models\ApiLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiUsageTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        // Ensure roles & permissions exist in test DB
        $permission = Permission::firstOrCreate(['name' => 'view api analytics', 'guard_name' => 'web']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo($permission);
    }

    public function test_api_call_without_headers_is_logged(): void
    {
        $response = $this->getJson('/api/v1/home');
        $response->assertStatus(200);

        $log = ApiLog::where('endpoint', '/api/v1/home')->first();
        $this->assertNotNull($log);
        $this->assertEquals('GET', $log->method);
        $this->assertEquals(200, $log->status_code);
        $this->assertContains($log->platform, ['web', 'other']);
    }

    public function test_api_call_with_android_headers_is_logged_properly(): void
    {
        $response = $this->withHeaders([
            'X-App-Platform' => 'android',
            'X-App-Version' => '2.4.1',
            'X-Device-Id' => 'android-uuid-999',
        ])->getJson('/api/v1/home');

        $response->assertStatus(200);

        $log = ApiLog::where('device_id', 'android-uuid-999')->first();
        $this->assertNotNull($log);
        $this->assertEquals('android', $log->platform);
        $this->assertEquals('2.4.1', $log->app_version);
        $this->assertEquals('android-uuid-999', $log->device_id);
    }

    public function test_api_call_with_ios_user_agent_is_detected(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15',
        ])->getJson('/api/v1/home');

        $response->assertStatus(200);

        $log = ApiLog::latest('id')->first();
        $this->assertNotNull($log);
        $this->assertEquals('ios', $log->platform);
    }

    public function test_authenticated_api_call_logs_user_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/home');
        $response->assertStatus(200);

        $log = ApiLog::latest('id')->first();
        $this->assertNotNull($log);
        $this->assertEquals($user->id, $log->user_id);
    }

    public function test_non_super_admin_cannot_access_analytics_dashboard(): void
    {
        $regularUser = User::factory()->create();

        $response = $this->actingAs($regularUser)->get(route('admin.api_usage.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_analytics_dashboard(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super admin');

        // Create some sample logs
        ApiLog::create([
            'method' => 'GET',
            'endpoint' => '/api/v1/meetings',
            'status_code' => 200,
            'response_time_ms' => 45,
            'platform' => 'android',
            'app_version' => '1.0.0',
            'created_at' => now(),
        ]);

        ApiLog::create([
            'method' => 'GET',
            'endpoint' => '/api/v1/jft',
            'status_code' => 200,
            'response_time_ms' => 30,
            'platform' => 'ios',
            'app_version' => '1.0.0',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.api_usage.index'));
        $response->assertStatus(200)
            ->assertSee(__('messages.api_analytics_title'))
            ->assertSee('/api/v1/meetings')
            ->assertSee('/api/v1/jft');
    }

    public function test_super_admin_can_export_csv(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super admin');

        ApiLog::create([
            'method' => 'GET',
            'endpoint' => '/api/v1/meetings',
            'status_code' => 200,
            'response_time_ms' => 50,
            'platform' => 'android',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.api_usage.export'));
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
    }

    public function test_aggregate_daily_stats_command(): void
    {
        $targetDate = now()->subDay()->toDateString();

        ApiLog::create([
            'method' => 'GET',
            'endpoint' => '/api/v1/home',
            'status_code' => 200,
            'response_time_ms' => 40,
            'platform' => 'android',
            'created_at' => now()->subDay()->startOfDay()->addHours(2),
        ]);

        ApiLog::create([
            'method' => 'GET',
            'endpoint' => '/api/v1/home',
            'status_code' => 500,
            'response_time_ms' => 120,
            'platform' => 'ios',
            'created_at' => now()->subDay()->startOfDay()->addHours(5),
        ]);

        $this->artisan("api-logs:aggregate --date={$targetDate}")
            ->assertSuccessful();

        $allStat = ApiDailyStat::where('date', $targetDate)->where('platform', 'all')->first();
        $this->assertNotNull($allStat);
        $this->assertEquals(2, $allStat->total_requests);
        $this->assertEquals(1, $allStat->successful_requests);
        $this->assertEquals(1, $allStat->server_error_requests);

        $androidStat = ApiDailyStat::where('date', $targetDate)->where('platform', 'android')->first();
        $this->assertNotNull($androidStat);
        $this->assertEquals(1, $androidStat->total_requests);
    }
}
