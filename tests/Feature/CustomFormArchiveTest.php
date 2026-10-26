<?php

namespace Tests\Feature;

use App\Models\CustomForm;
use App\Models\CustomFormField;
use App\Models\CustomFormSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CustomFormArchiveTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $rsc = Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'manage own forms', 'guard_name' => 'web']);
        $rsc->givePermissionTo($permission);

        $this->user = User::factory()->create();
        $this->user->assignRole('rsc');
    }

    public function test_can_archive_an_active_form()
    {
        $this->actingAs($this->user);

        $form = CustomForm::create([
            'title' => 'Old Survey Form',
            'type' => 'survey',
            'status' => 'published',
            'user_id' => $this->user->id,
        ]);

        $response = $this->post(route('forms.archive', $form->id));
        $response->assertSessionHas('success', __('messages.form_archived_success'));

        $this->assertEquals('archived', $form->fresh()->status);
        $this->assertTrue($form->fresh()->isArchived());
    }

    public function test_can_unarchive_an_archived_form()
    {
        $this->actingAs($this->user);

        $form = CustomForm::create([
            'title' => 'Archived Survey',
            'type' => 'survey',
            'status' => 'archived',
            'user_id' => $this->user->id,
        ]);

        $response = $this->post(route('forms.unarchive', $form->id));
        $response->assertSessionHas('success', __('messages.form_unarchived_success'));

        $this->assertEquals('unpublished', $form->fresh()->status);
        $this->assertFalse($form->fresh()->isArchived());
    }

    public function test_forms_index_tabs_filter_active_and_archived_forms()
    {
        $this->actingAs($this->user);

        $activeForm = CustomForm::create([
            'title' => 'Active Form Alpha',
            'type' => 'survey',
            'status' => 'published',
            'user_id' => $this->user->id,
        ]);

        $archivedForm = CustomForm::create([
            'title' => 'Archived Form Omega',
            'type' => 'survey',
            'status' => 'archived',
            'user_id' => $this->user->id,
        ]);

        // Default tab (active forms)
        $response = $this->get(route('forms.index'));
        $response->assertStatus(200);
        $response->assertSee('Active Form Alpha');
        $response->assertDontSee('Archived Form Omega');
        $response->assertViewHas('activeCount', 1);
        $response->assertViewHas('archivedCount', 1);

        // Archived tab
        $responseArchived = $this->get(route('forms.index', ['tab' => 'archived']));
        $responseArchived->assertStatus(200);
        $responseArchived->assertSee('Archived Form Omega');
        $responseArchived->assertDontSee('Active Form Alpha');

        // All tab
        $responseAll = $this->get(route('forms.index', ['tab' => 'all']));
        $responseAll->assertStatus(200);
        $responseAll->assertSee('Active Form Alpha');
        $responseAll->assertSee('Archived Form Omega');
    }

    public function test_visiting_public_link_for_archived_form_shows_closed_notice()
    {
        $form = CustomForm::create([
            'title' => 'Annual Convention Registration 2025',
            'type' => 'event_registration',
            'status' => 'archived',
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('forms.show.public', $form->slug));
        $response->assertStatus(200);
        $response->assertSee('Annual Convention Registration 2025');
        $response->assertSee(__('messages.form_archived_notice_title'));
        $response->assertSee(__('messages.form_archived_notice_desc'));
    }

    public function test_submitting_archived_form_is_rejected()
    {
        $form = CustomForm::create([
            'title' => 'Closed Survey',
            'type' => 'survey',
            'status' => 'archived',
            'user_id' => $this->user->id,
        ]);

        $response = $this->post(route('forms.submit.public', $form->slug), [
            'some_field' => 'data',
        ]);

        $response->assertRedirect(route('forms.show.public', $form->slug));
        $this->assertEquals(0, $form->submissions()->count());
    }

    public function test_editing_archived_form_fields_is_blocked_until_unarchived()
    {
        $this->actingAs($this->user);

        $form = CustomForm::create([
            'title' => 'Locked Archived Form',
            'type' => 'survey',
            'status' => 'archived',
            'user_id' => $this->user->id,
        ]);

        $response = $this->put(route('forms.update', $form->id), [
            'title' => 'Updated Title While Archived',
            'type' => 'survey',
            'status' => 'archived',
        ]);

        $response->assertSessionHas('error', __('messages.form_archived_locked'));
        $this->assertEquals('Locked Archived Form', $form->fresh()->title);

        // However, updating status to 'unpublished' to unarchive works
        $unarchiveResponse = $this->put(route('forms.update', $form->id), [
            'title' => 'Unlocked Form',
            'type' => 'survey',
            'status' => 'unpublished',
        ]);

        $unarchiveResponse->assertRedirect(route('forms.index'));
        $this->assertEquals('Unlocked Form', $form->fresh()->title);
        $this->assertEquals('unpublished', $form->fresh()->status);
    }

    public function test_submissions_and_reports_remain_accessible_for_archived_forms()
    {
        $this->actingAs($this->user);

        $form = CustomForm::create([
            'title' => 'Historical Submissions Form',
            'type' => 'survey',
            'status' => 'archived',
            'user_id' => $this->user->id,
        ]);

        $field = CustomFormField::create([
            'custom_form_id' => $form->id,
            'label' => 'Full Name',
            'type' => 'text',
            'required' => true,
            'sort_order' => 1,
        ]);

        CustomFormSubmission::create([
            'custom_form_id' => $form->id,
            'data' => [$field->id => 'John Doe'],
        ]);

        $response = $this->get(route('forms.report', $form->id));
        $response->assertStatus(200);
        $response->assertSee('Historical Submissions Form');
        $response->assertSee('John Doe');
    }
}
