<?php

namespace Tests\Feature\Api;

use App\Models\ChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ChangeRequestApiTest extends TestCase
{
    use RefreshDatabase;

    private User $memberUser;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Storage::fake('public');

        Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);

        $this->memberUser = User::factory()->create();
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('super admin');
    }

    public function test_unauthenticated_user_cannot_access_change_requests(): void
    {
        $response = $this->getJson('/api/v1/change-requests');
        $response->assertStatus(401);

        $postResponse = $this->postJson('/api/v1/change-requests', [
            'request_type' => 'general',
            'subject'      => 'Test',
            'description'  => 'Description',
        ]);
        $postResponse->assertStatus(401);
    }

    public function test_authenticated_user_can_submit_change_request_with_attachment(): void
    {
        Sanctum::actingAs($this->memberUser);

        $file = UploadedFile::fake()->create('details.pdf', 1024, 'application/pdf');

        $response = $this->postJson('/api/v1/change-requests', [
            'request_type' => 'meetings_groups',
            'subject'      => 'طلب تعديل عنوان اجتماع',
            'description'  => 'يرجى تغيير عنوان اجتماع الأربعاء',
            'attachment'   => $file,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.request_type', 'meetings_groups')
            ->assertJsonPath('data.subject', 'طلب تعديل عنوان اجتماع')
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('change_requests', [
            'user_id'      => $this->memberUser->id,
            'request_type' => 'meetings_groups',
            'status'       => 'pending',
        ]);
    }

    public function test_store_validates_required_fields_and_attachment(): void
    {
        Sanctum::actingAs($this->memberUser);

        $response = $this->postJson('/api/v1/change-requests', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['request_type', 'subject', 'description']);

        // Invalid file type
        $invalidFile = UploadedFile::fake()->create('bad.exe', 500);
        $invalidResponse = $this->postJson('/api/v1/change-requests', [
            'request_type' => 'general',
            'subject'      => 'Test',
            'description'  => 'Test',
            'attachment'   => $invalidFile,
        ]);
        $invalidResponse->assertStatus(422)
            ->assertJsonValidationErrors(['attachment']);
    }

    public function test_user_lists_only_own_requests_while_admin_lists_all(): void
    {
        $otherUser = User::factory()->create();

        ChangeRequest::create([
            'user_id'      => $this->memberUser->id,
            'request_type' => 'general',
            'subject'      => 'Member Request',
            'description'  => 'Details',
            'status'       => 'pending',
        ]);

        ChangeRequest::create([
            'user_id'      => $otherUser->id,
            'request_type' => 'general',
            'subject'      => 'Other Request',
            'description'  => 'Details',
            'status'       => 'pending',
        ]);

        // Regular member sees only 1
        Sanctum::actingAs($this->memberUser);
        $memberResponse = $this->getJson('/api/v1/change-requests');
        $memberResponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.subject', 'Member Request');

        // Admin sees both (2)
        Sanctum::actingAs($this->adminUser);
        $adminResponse = $this->getJson('/api/v1/change-requests');
        $adminResponse->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_admin_can_update_request_status(): void
    {
        $request = ChangeRequest::create([
            'user_id'      => $this->memberUser->id,
            'request_type' => 'committee_info',
            'subject'      => 'تحديث بيانات اللجنة',
            'description'  => 'تفاصيل التحديث',
            'status'       => 'pending',
        ]);

        // Regular member cannot update status
        Sanctum::actingAs($this->memberUser);
        $forbiddenResponse = $this->patchJson("/api/v1/change-requests/{$request->id}/status", [
            'status' => 'in_progress',
        ]);
        $forbiddenResponse->assertStatus(403);

        // Super Admin can update status
        Sanctum::actingAs($this->adminUser);
        $adminResponse = $this->patchJson("/api/v1/change-requests/{$request->id}/status", [
            'status' => 'in_progress',
        ]);
        $adminResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'in_progress');

        $this->assertDatabaseHas('change_requests', [
            'id'     => $request->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_user_can_delete_own_pending_request(): void
    {
        Sanctum::actingAs($this->memberUser);

        $request = ChangeRequest::create([
            'user_id'      => $this->memberUser->id,
            'request_type' => 'other',
            'subject'      => 'طلب للإلغاء',
            'description'  => 'تم الحل',
            'status'       => 'pending',
        ]);

        $response = $this->deleteJson("/api/v1/change-requests/{$request->id}");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('change_requests', ['id' => $request->id]);
    }
}
