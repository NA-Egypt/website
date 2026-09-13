<?php

namespace Tests\Feature\Api;

use App\Models\DirectOnlineGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DirectOnlineGroupApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_public_can_list_direct_online_groups(): void
    {
        DirectOnlineGroup::create([
            'ar_name'     => 'مجموعة التعافي أونلاين',
            'en_name'     => 'Online Recovery Group',
            'phone'       => '+201000000001',
            'location'    => 'https://zoom.us/j/123456789',
            'user_id'     => $this->user->id,
        ]);

        $response = $this->getJson('/api/v1/direct-online-groups');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.ar_name', 'مجموعة التعافي أونلاين')
            ->assertJsonPath('data.0.location', 'https://zoom.us/j/123456789');
    }

    public function test_public_can_get_single_direct_online_group(): void
    {
        $group = DirectOnlineGroup::create([
            'ar_name'     => 'مجموعة الأمل أونلاين',
            'en_name'     => 'Hope Online Group',
            'phone'       => '+201000000002',
            'location'    => 'https://zoom.us/j/987654321',
            'user_id'     => $this->user->id,
        ]);

        $response = $this->getJson("/api/v1/direct-online-groups/{$group->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $group->id)
            ->assertJsonPath('data.en_name', 'Hope Online Group');
    }

    public function test_unauthenticated_user_cannot_create_direct_online_group(): void
    {
        $response = $this->postJson('/api/v1/direct-online-groups', [
            'ar_name' => 'مجموعة جديدة',
            'en_name' => 'New Group',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_direct_online_group(): void
    {
        Sanctum::actingAs($this->user);

        $payload = [
            'ar_name'     => 'مجموعة النور',
            'en_name'     => 'Noor Online Group',
            'ar_gsr_name' => 'خالد',
            'en_gsr_name' => 'Khaled',
            'phone'       => '+201011122233',
            'location'    => 'https://zoom.us/j/555444333',
        ];

        $response = $this->postJson('/api/v1/direct-online-groups', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.ar_name', 'مجموعة النور')
            ->assertJsonPath('data.en_name', 'Noor Online Group')
            ->assertJsonPath('data.location', 'https://zoom.us/j/555444333');

        $this->assertDatabaseHas('direct_online_groups', [
            'ar_name' => 'مجموعة النور',
            'en_name' => 'Noor Online Group',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/direct-online-groups', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ar_name', 'en_name', 'location']);
    }

    public function test_authenticated_user_can_update_direct_online_group(): void
    {
        Sanctum::actingAs($this->user);

        $group = DirectOnlineGroup::create([
            'ar_name'  => 'الاسم القديم',
            'en_name'  => 'Old Name',
            'location' => 'https://zoom.us/j/111222333',
            'user_id'  => $this->user->id,
        ]);

        $response = $this->putJson("/api/v1/direct-online-groups/{$group->id}", [
            'ar_name' => 'الاسم الجديد',
            'en_name' => 'Updated Name',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.ar_name', 'الاسم الجديد')
            ->assertJsonPath('data.en_name', 'Updated Name');

        $this->assertDatabaseHas('direct_online_groups', [
            'id'      => $group->id,
            'ar_name' => 'الاسم الجديد',
        ]);
    }

    public function test_authenticated_user_can_delete_direct_online_group(): void
    {
        Sanctum::actingAs($this->user);

        $group = DirectOnlineGroup::create([
            'ar_name'  => 'للحذف',
            'en_name'  => 'To Delete',
            'location' => 'https://zoom.us/j/999888777',
            'user_id'  => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/v1/direct-online-groups/{$group->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('direct_online_groups', ['id' => $group->id]);
    }
}
