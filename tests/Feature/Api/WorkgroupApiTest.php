<?php

namespace Tests\Feature\Api;

use App\Models\ServiceCommittee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkgroupApiTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $committeeUser;
    private ServiceCommittee $parentCommittee;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Committees', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Workgroups', 'guard_name' => 'web']);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('super admin');

        $this->committeeUser = User::factory()->create();
        $this->committeeUser->assignRole('Committees');

        $this->parentCommittee = ServiceCommittee::create([
            'ar_name' => 'لجنة العلاقات العامة',
            'en_name' => 'PR Committee',
            'user_id' => $this->committeeUser->id,
            'email'   => $this->committeeUser->email,
        ]);
    }

    public function test_public_can_list_workgroups(): void
    {
        ServiceCommittee::create([
            'parent_id'      => $this->parentCommittee->id,
            'workgroup_type' => 'standing',
            'status'         => 'active',
            'ar_name'        => 'مجموعة عمل السوشيال ميديا',
            'en_name'        => 'Social Media Workgroup',
        ]);

        $response = $this->getJson('/api/v1/workgroups');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.en_name', 'Social Media Workgroup');
    }

    public function test_public_can_get_single_workgroup(): void
    {
        $workgroup = ServiceCommittee::create([
            'parent_id'      => $this->parentCommittee->id,
            'workgroup_type' => 'standing',
            'status'         => 'active',
            'ar_name'        => 'مجموعة عمل المعارض',
            'en_name'        => 'Exhibitions Workgroup',
        ]);

        $response = $this->getJson("/api/v1/workgroups/{$workgroup->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $workgroup->id)
            ->assertJsonPath('data.ar_name', 'مجموعة عمل المعارض');
    }

    public function test_unauthenticated_user_cannot_create_workgroup(): void
    {
        $response = $this->postJson('/api/v1/workgroups', [
            'parent_id'      => $this->parentCommittee->id,
            'workgroup_type' => 'standing',
            'status'         => 'active',
            'ar_name'        => 'مجموعة جديدة',
            'en_name'        => 'New Workgroup',
        ]);

        $response->assertStatus(401);
    }

    public function test_authorized_committee_user_can_create_workgroup(): void
    {
        Sanctum::actingAs($this->committeeUser);

        $payload = [
            'parent_id'      => $this->parentCommittee->id,
            'workgroup_type' => 'standing',
            'status'         => 'active',
            'ar_name'        => 'مجموعة الرد الهاتفي',
            'en_name'        => 'Helpline Workgroup',
            'chairman_name'  => 'محمد',
            'chairman_phone' => '+201009988776',
        ];

        $response = $this->postJson('/api/v1/workgroups', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.ar_name', 'مجموعة الرد الهاتفي')
            ->assertJsonPath('data.en_name', 'Helpline Workgroup')
            ->assertJsonPath('data.parent_id', $this->parentCommittee->id);

        $this->assertDatabaseHas('service_committees', [
            'parent_id' => $this->parentCommittee->id,
            'ar_name'   => 'مجموعة الرد الهاتفي',
        ]);
    }

    public function test_user_cannot_create_workgroup_under_unowned_committee(): void
    {
        $otherUser = User::factory()->create();
        $otherUser->assignRole('Committees');

        Sanctum::actingAs($otherUser);

        $payload = [
            'parent_id'      => $this->parentCommittee->id,
            'workgroup_type' => 'standing',
            'status'         => 'active',
            'ar_name'        => 'محاولة غير مصرحة',
            'en_name'        => 'Unauthorized Attempt',
        ];

        $response = $this->postJson('/api/v1/workgroups', $payload);
        $response->assertStatus(403);
    }

    public function test_store_validates_required_fields(): void
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('/api/v1/workgroups', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['parent_id', 'workgroup_type', 'status', 'ar_name', 'en_name']);
    }

    public function test_authorized_user_can_update_workgroup(): void
    {
        Sanctum::actingAs($this->committeeUser);

        $workgroup = ServiceCommittee::create([
            'parent_id'      => $this->parentCommittee->id,
            'workgroup_type' => 'ad_hoc',
            'status'         => 'active',
            'ar_name'        => 'مجموعة قديمة',
            'en_name'        => 'Old Workgroup',
        ]);

        $response = $this->putJson("/api/v1/workgroups/{$workgroup->id}", [
            'ar_name' => 'مجموعة محدثة',
            'en_name' => 'Updated Workgroup',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.ar_name', 'مجموعة محدثة')
            ->assertJsonPath('data.en_name', 'Updated Workgroup');

        $this->assertDatabaseHas('service_committees', [
            'id'      => $workgroup->id,
            'ar_name' => 'مجموعة محدثة',
        ]);
    }

    public function test_authorized_user_can_delete_workgroup(): void
    {
        Sanctum::actingAs($this->committeeUser);

        $workgroup = ServiceCommittee::create([
            'parent_id'      => $this->parentCommittee->id,
            'workgroup_type' => 'ad_hoc',
            'status'         => 'active',
            'ar_name'        => 'مجموعة مؤقتة للحذف',
            'en_name'        => 'To Delete Workgroup',
        ]);

        $response = $this->deleteJson("/api/v1/workgroups/{$workgroup->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('service_committees', ['id' => $workgroup->id]);
    }
}
