<?php

namespace Tests\Feature;

use App\Exceptions\UserFacingException;
use App\Models\InventoryItem;
use App\Models\User;
use App\Services\HelplineReportService;
use App\Services\InventoryLedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExceptionHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_unexpected_exception_in_store_controller_returns_generic_message_without_leaking_details(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

        $item = InventoryItem::create([
            'name' => 'Basic Text',
            'name_en' => 'Basic Text',
            'store_quantity' => 10,
            'lit_quantity' => 5,
            'selling_price' => 100,
        ]);

        $mockLedger = Mockery::mock(InventoryLedgerService::class)->makePartial();
        $mockLedger->shouldReceive('createTransferSlip')
            ->once()
            ->andThrow(new \RuntimeException('SQLSTATE[42S22]: Sensitive internal SQL details'));
        $this->app->instance(InventoryLedgerService::class, $mockLedger);

        $response = $this->actingAs($user)->postJson(route('store.bulk_inline_update'), [
            'items' => [
                [
                    'id' => $item->id,
                    'selling_price' => 100,
                    'receive_qty' => 0,
                    'transfer_qty' => 2,
                ],
            ],
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => __('errors.generic'),
            ]);
        $this->assertStringNotContainsString('SQLSTATE', $response->json('message'));
    }

    public function test_user_facing_exception_message_is_shown_to_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

        $mockService = Mockery::mock(HelplineReportService::class);
        $mockService->shouldReceive('getAvailableCycles')->andReturn([]);
        $mockService->shouldReceive('getCurrentCycleWindow')->andReturn([
            'start' => now()->subMonth(),
            'end' => now(),
        ]);
        $mockService->shouldReceive('syncToWorkgroupReport')
            ->once()
            ->andThrow(new UserFacingException('رسالة خطأ واضحة للمستخدم', 'custom_domain_error', 422));
        $this->app->instance(HelplineReportService::class, $mockService);

        $response = $this->actingAs($user)->post(route('helpline.sync.workgroup'));

        $response->assertRedirect()
            ->assertSessionHas('error', 'رسالة خطأ واضحة للمستخدم');
    }

    public function test_unexpected_exception_in_helpline_sync_shows_generic_error(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

        $mockService = Mockery::mock(HelplineReportService::class);
        $mockService->shouldReceive('getAvailableCycles')->andReturn([]);
        $mockService->shouldReceive('getCurrentCycleWindow')->andReturn([
            'start' => now()->subMonth(),
            'end' => now(),
        ]);
        $mockService->shouldReceive('syncToWorkgroupReport')
            ->once()
            ->andThrow(new \RuntimeException('Internal DB connection failure'));
        $this->app->instance(HelplineReportService::class, $mockService);

        $response = $this->actingAs($user)->post(route('helpline.sync.workgroup'));

        $response->assertRedirect()
            ->assertSessionHas('error', __('errors.generic'));
    }
}
