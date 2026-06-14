<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CustomForm;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomFormRscPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);
    }

    public function test_rsc_role_can_view_all_forms_on_index()
    {
        $creator = User::factory()->create();
        
        $form1 = CustomForm::create([
            'title' => 'Form 1 By Other',
            'type' => 'survey',
            'status' => 'draft',
            'user_id' => $creator->id,
            'slug' => 'slug1',
        ]);

        $rscUser = User::factory()->create();
        $rscUser->assignRole('rsc');

        $this->actingAs($rscUser);

        $response = $this->get(route('forms.index'));
        $response->assertStatus(200);
        $response->assertSee('Form 1 By Other');
    }

    public function test_rsc_role_can_edit_others_forms()
    {
        $creator = User::factory()->create();
        
        $form = CustomForm::create([
            'title' => 'Form By Other',
            'type' => 'survey',
            'status' => 'draft',
            'user_id' => $creator->id,
            'slug' => 'slug2',
        ]);

        $rscUser = User::factory()->create();
        $rscUser->assignRole('rsc');

        $this->actingAs($rscUser);

        // Can view edit page
        $this->get(route('forms.edit', $form->id))
            ->assertStatus(200);

        // Can update form
        $response = $this->put(route('forms.update', $form->id), [
            'title' => 'Updated By RSC',
            'type' => 'survey',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('forms.index'));
        $this->assertDatabaseHas('custom_forms', [
            'id' => $form->id,
            'title' => 'Updated By RSC',
            'status' => 'published',
        ]);
    }
}
