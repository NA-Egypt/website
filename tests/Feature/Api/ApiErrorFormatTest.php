<?php

namespace Tests\Feature\Api;

use App\Exceptions\UserFacingException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiErrorFormatTest extends TestCase
{
    use RefreshDatabase;

    public function test_401_unauthenticated_returns_unified_error_format(): void
    {
        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(401)
            ->assertJson([
                'message' => __('errors.unauthenticated'),
                'code' => 'unauthenticated',
                'errors' => null,
            ]);
    }

    public function test_403_forbidden_returns_unified_error_format(): void
    {
        $plainUser = User::factory()->create();
        Sanctum::actingAs($plainUser);

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(403)
            ->assertJson([
                'message' => __('errors.forbidden'),
                'code' => 'forbidden',
                'errors' => null,
            ]);
    }

    public function test_404_not_found_returns_unified_error_format(): void
    {
        $response = $this->getJson('/api/v1/groups/999999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => __('errors.not_found'),
                'code' => 'not_found',
                'errors' => null,
            ]);
    }

    public function test_422_validation_failed_returns_unified_error_format_compatible_with_laravel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/users', []);

        $response->assertStatus(422)
            ->assertJsonPath('code', 'validation_failed')
            ->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_429_too_many_requests_returns_unified_error_format(): void
    {
        Route::get('/api/v1/_test-throttle', function () {
            throw new ThrottleRequestsException('Too Many Attempts.');
        });

        $response = $this->getJson('/api/v1/_test-throttle');

        $response->assertStatus(429)
            ->assertJson([
                'message' => __('errors.too_many_requests'),
                'code' => 'too_many_requests',
                'errors' => null,
            ]);
    }

    public function test_500_server_error_returns_unified_error_format(): void
    {
        Route::get('/api/v1/_test-server-error', function () {
            throw new \RuntimeException('Sensitive database crash details');
        });

        $response = $this->getJson('/api/v1/_test-server-error');

        $response->assertStatus(500)
            ->assertJson([
                'message' => __('errors.generic'),
                'code' => 'server_error',
                'errors' => null,
            ]);
    }

    public function test_user_facing_exception_returns_domain_code_and_message(): void
    {
        Route::get('/api/v1/_test-domain-error', function () {
            throw new UserFacingException('الرصيد غير كافٍ', 'insufficient_stock', 422);
        });

        $response = $this->getJson('/api/v1/_test-domain-error');

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'الرصيد غير كافٍ',
                'code' => 'insufficient_stock',
                'errors' => null,
            ]);
    }
}
