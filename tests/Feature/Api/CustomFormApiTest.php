<?php

namespace Tests\Feature\Api;

use App\Models\CustomForm;
use App\Models\CustomFormField;
use App\Models\CustomFormSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomFormApiTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $normalUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('super admin');

        $this->normalUser = User::factory()->create();
    }

    public function test_public_can_view_published_form_by_slug(): void
    {
        $form = CustomForm::create([
            'title'   => 'استبيان مؤتمر مصر',
            'status'  => 'published',
            'slug'    => 'egypt-convention-survey',
            'user_id' => $this->adminUser->id,
        ]);

        $form->fields()->create([
            'label'      => 'الاسم بالكامل',
            'type'       => 'text',
            'required'   => true,
            'sort_order' => 1,
        ]);

        $response = $this->getJson('/api/v1/forms/public/egypt-convention-survey');

        $response->assertStatus(200)
            ->assertJsonPath('data.slug', 'egypt-convention-survey')
            ->assertJsonPath('data.title', 'استبيان مؤتمر مصر')
            ->assertJsonCount(1, 'data.fields');
    }

    public function test_public_cannot_view_draft_form(): void
    {
        CustomForm::create([
            'title'   => 'مسودة نموذج',
            'status'  => 'draft',
            'slug'    => 'draft-form',
            'user_id' => $this->adminUser->id,
        ]);

        $response = $this->getJson('/api/v1/forms/public/draft-form');
        $response->assertStatus(404);
    }

    public function test_public_can_submit_response_to_published_form(): void
    {
        $form = CustomForm::create([
            'title'   => 'استبيان التعافي',
            'status'  => 'published',
            'slug'    => 'recovery-survey',
            'user_id' => $this->adminUser->id,
        ]);

        $nameField = $form->fields()->create([
            'label'    => 'الاسم',
            'type'     => 'text',
            'required' => true,
        ]);

        $emailField = $form->fields()->create([
            'label'    => 'البريد الإلكتروني',
            'type'     => 'email',
            'required' => false,
        ]);

        $payload = [
            'field_' . $nameField->id  => 'أحمد علي',
            'field_' . $emailField->id => 'ahmed@example.com',
        ];

        $response = $this->postJson('/api/v1/forms/public/recovery-survey/submit', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.custom_form_id', $form->id);

        $this->assertDatabaseHas('custom_form_submissions', [
            'custom_form_id' => $form->id,
        ]);
    }

    public function test_public_submit_validates_required_fields(): void
    {
        $form = CustomForm::create([
            'title'   => 'استبيان إلزامي',
            'status'  => 'published',
            'slug'    => 'mandatory-form',
            'user_id' => $this->adminUser->id,
        ]);

        $nameField = $form->fields()->create([
            'label'    => 'الاسم',
            'type'     => 'text',
            'required' => true,
        ]);

        $response = $this->postJson('/api/v1/forms/public/mandatory-form/submit', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['field_' . $nameField->id]);
    }

    public function test_unauthenticated_user_cannot_access_forms_crud(): void
    {
        $response = $this->getJson('/api/v1/forms');
        $response->assertStatus(401);

        $postResponse = $this->postJson('/api/v1/forms', [
            'title'  => 'نموذج جديد',
            'status' => 'draft',
        ]);
        $postResponse->assertStatus(401);
    }

    public function test_authenticated_user_can_create_form_with_fields(): void
    {
        Sanctum::actingAs($this->adminUser);

        $payload = [
            'title'  => 'نموذج تسجيل متطوعين',
            'status' => 'published',
            'slug'   => 'volunteers-signup',
            'fields' => [
                [
                    'label'    => 'الاسم',
                    'type'     => 'text',
                    'required' => true,
                ],
                [
                    'label'    => 'سنوات التعافي',
                    'type'     => 'number',
                    'required' => true,
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/forms', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'نموذج تسجيل متطوعين')
            ->assertJsonPath('data.slug', 'volunteers-signup')
            ->assertJsonCount(2, 'data.fields');

        $this->assertDatabaseHas('custom_forms', [
            'slug' => 'volunteers-signup',
        ]);
    }

    public function test_authorized_user_can_view_form_submissions(): void
    {
        Sanctum::actingAs($this->adminUser);

        $form = CustomForm::create([
            'title'   => 'نموذج مع إجابات',
            'status'  => 'published',
            'slug'    => 'form-with-subs',
            'user_id' => $this->adminUser->id,
        ]);

        CustomFormSubmission::create([
            'custom_form_id' => $form->id,
            'data'           => ['1' => 'Answer 1'],
        ]);

        $response = $this->getJson("/api/v1/forms/{$form->id}/submissions");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.custom_form_id', $form->id);
    }

    public function test_authorized_user_can_delete_form(): void
    {
        Sanctum::actingAs($this->adminUser);

        $form = CustomForm::create([
            'title'   => 'نموذج للحذف',
            'status'  => 'draft',
            'slug'    => 'form-to-delete',
            'user_id' => $this->adminUser->id,
        ]);

        $response = $this->deleteJson("/api/v1/forms/{$form->id}");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('custom_forms', ['id' => $form->id]);
    }
}
