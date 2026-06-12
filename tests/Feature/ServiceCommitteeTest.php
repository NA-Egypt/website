<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceCommittee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCommitteeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable localization redirect middlewares to prevent redirects to /ar in test environment
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
    }

    public function test_store_service_committee_with_numeric_email_maps_user_correctly()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super admin');

        $targetUser = User::factory()->create(['email' => 'target@naegypt.org']);

        $this->actingAs($admin);

        $response = $this->post(route('serviceCommittee.store'), [
            'ar_name' => 'اللجنة التقنية',
            'en_name' => 'Technical Committee',
            'email' => (string)$targetUser->id, // submits numeric user ID
            'chairman_name' => 'Chairman Name',
            'chairman_phone' => '12345678',
            'location' => 'Cairo',
            'ar_address' => 'العنوان العربي',
            'en_address' => 'English Address',
            'notes' => 'Some meetings notes',
        ]);

        $response->assertRedirect(route('serviceCommittee.index'));

        $this->assertDatabaseHas('service_committees', [
            'ar_name' => 'اللجنة التقنية',
            'user_id' => $targetUser->id,
            'email' => 'target@naegypt.org',
        ]);
    }

    public function test_update_service_committee_with_numeric_email_maps_user_correctly()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super admin');

        $targetUser1 = User::factory()->create(['email' => 'target1@naegypt.org']);
        $targetUser2 = User::factory()->create(['email' => 'target2@naegypt.org']);

        $committee = ServiceCommittee::create([
            'ar_name' => 'اللجنة القديمة',
            'en_name' => 'Old Committee',
            'email' => $targetUser1->email,
            'user_id' => $targetUser1->id,
            'chairman_name' => 'Chairman Name',
            'chairman_phone' => '12345678',
            'location' => 'Cairo',
            'ar_address' => 'العنوان العربي',
            'en_address' => 'English Address',
            'notes' => 'Some meetings notes',
        ]);

        $this->actingAs($admin);

        $response = $this->put(route('serviceCommittee.update', $committee->id), [
            'ar_name' => 'اللجنة المحدثة',
            'en_name' => 'Updated Committee',
            'email' => (string)$targetUser2->id, // update to targetUser2 ID
            'chairman_name' => 'New Chairman',
            'chairman_phone' => '87654321',
            'location' => 'Giza',
            'ar_address' => 'العنوان الجديد',
            'en_address' => 'New Address',
            'notes' => 'Updated notes',
        ]);

        $response->assertRedirect(route('serviceCommittee.index'));

        $this->assertDatabaseHas('service_committees', [
            'id' => $committee->id,
            'ar_name' => 'اللجنة المحدثة',
            'user_id' => $targetUser2->id,
            'email' => 'target2@naegypt.org',
        ]);
    }

    public function test_api_store_and_update_service_committee_maps_user_correctly()
    {
        $admin = User::factory()->create();
        // Sanctum authentication
        $this->actingAs($admin, 'sanctum');

        $targetUser = User::factory()->create(['email' => 'api-target@naegypt.org']);

        // 1. API Store
        $response = $this->postJson('/api/service-committees', [
            'ar_name' => 'لجنة API',
            'en_name' => 'API Committee',
            'email' => (string)$targetUser->id,
            'chairman_name' => 'API Chairman',
            'chairman_phone' => '11111111',
            'location' => 'Cairo API',
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'notes' => 'Notes',
        ]);

        $response->assertStatus(201);
        $committeeId = $response->json('data.id');

        $this->assertDatabaseHas('service_committees', [
            'id' => $committeeId,
            'user_id' => $targetUser->id,
            'email' => 'api-target@naegypt.org',
        ]);

        // 2. API Update
        $targetUser2 = User::factory()->create(['email' => 'api-target2@naegypt.org']);
        $response = $this->putJson("/api/service-committees/{$committeeId}", [
            'ar_name' => 'لجنة API محدثة',
            'en_name' => 'API Committee Updated',
            'email' => (string)$targetUser2->id,
            'chairman_name' => 'API Chairman 2',
            'chairman_phone' => '22222222',
            'location' => 'Cairo API 2',
            'ar_address' => 'العنوان 2',
            'en_address' => 'Address 2',
            'notes' => 'Notes 2',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('service_committees', [
            'id' => $committeeId,
            'user_id' => $targetUser2->id,
            'email' => 'api-target2@naegypt.org',
        ]);
    }
}
