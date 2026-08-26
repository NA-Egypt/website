<?php

namespace Tests\Feature;

use App\Models\CustomForm;
use App\Models\CustomFormField;
use App\Models\CustomFormSubmission;
use App\Models\User;
use App\Models\ServiceCommittee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomFormFeaturesTest extends TestCase
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
        $rsc = Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage own forms', 'guard_name' => 'web']);
        $rsc->givePermissionTo($permission);
    }

    public function test_can_create_service_position_application_form_with_table_and_yes_no_fields()
    {
        $user = User::factory()->create();
        $user->assignRole('rsc');
        $this->actingAs($user);

        $response = $this->post(route('forms.store'), [
            'title' => 'Service Position Application 2026',
            'type' => 'service_position_application',
            'status' => 'published',
            'settings' => [
                'icon' => 'bi-person-badge',
                'subtitle' => 'Please fill out all service requirements',
            ],
            'fields' => [
                [
                    'label' => 'Clean Time (Years)',
                    'type' => 'number',
                    'required' => '1',
                ],
                [
                    'label' => 'Previous Service Experience',
                    'type' => 'table',
                    'options' => 'Position, Committee, Start Year, End Year',
                    'required' => '1',
                ],
                [
                    'label' => 'Have you ever been removed from a service position?',
                    'type' => 'yes_no_textbox',
                    'placeholder' => 'If yes, please explain the circumstances...',
                    'required' => '1',
                ],
                [
                    'label' => 'Preferred Committee',
                    'type' => 'committees',
                    'required' => '0',
                ],
            ],
        ]);

        $response->assertRedirect(route('forms.index'));

        $this->assertDatabaseHas('custom_forms', [
            'title' => 'Service Position Application 2026',
            'type' => 'service_position_application',
            'status' => 'published',
        ]);

        $form = CustomForm::where('title', 'Service Position Application 2026')->first();
        $this->assertCount(4, $form->fields);

        $tableField = $form->fields()->where('type', 'table')->first();
        $this->assertNotNull($tableField);
        $this->assertEquals(['Position', 'Committee', 'Start Year', 'End Year'], $tableField->options['columns']);

        $yesNoField = $form->fields()->where('type', 'yes_no_textbox')->first();
        $this->assertNotNull($yesNoField);
        $this->assertEquals('If yes, please explain the circumstances...', $yesNoField->options['placeholder']);
    }

    public function test_can_view_and_submit_service_position_application_form()
    {
        $user = User::factory()->create();

        $form = CustomForm::create([
            'title' => 'Public Service Form',
            'type' => 'service_position_application',
            'status' => 'published',
            'user_id' => $user->id,
            'slug' => 'public-service-form',
            'settings' => ['icon' => 'bi-person-badge'],
        ]);

        $fNumber = CustomFormField::create([
            'custom_form_id' => $form->id,
            'label' => 'Years Clean',
            'type' => 'number',
            'required' => true,
            'order' => 1,
        ]);

        $fTable = CustomFormField::create([
            'custom_form_id' => $form->id,
            'label' => 'Service History',
            'type' => 'table',
            'required' => true,
            'order' => 2,
            'options' => ['columns' => ['Position', 'Start Year', 'End Year']],
        ]);

        $fYesNo = CustomFormField::create([
            'custom_form_id' => $form->id,
            'label' => 'Working all 12 Steps?',
            'type' => 'yes_no_textbox',
            'required' => true,
            'order' => 3,
            'options' => ['placeholder' => 'Provide details...'],
        ]);

        $fDynamic = CustomFormField::create([
            'custom_form_id' => $form->id,
            'label' => 'Service Committee',
            'type' => 'committees',
            'required' => true,
            'order' => 4,
        ]);

        // 1. Visit public form
        $response = $this->get(route('forms.show.public', $form->slug));
        $response->assertStatus(200);
        $response->assertSee('Public Service Form');
        $response->assertSee('Years Clean');
        $response->assertSee('Service History');
        $response->assertSee('Working all 12 Steps?');
        $response->assertSee('Service Committee');

        // 2. Submit form with repeatable table rows, yes_no_textbox with details, and dynamic other option
        $submitData = [
            'field_' . $fNumber->id => 5,
            'field_' . $fTable->id => [
                [
                    'Position' => 'GSR',
                    'Start Year' => '2022',
                    'End Year' => '2024',
                ],
                [
                    'Position' => 'Literature Chair',
                    'Start Year' => '2024',
                    'End Year' => '2026',
                ],
            ],
            'field_' . $fYesNo->id => 'yes',
            'field_' . $fYesNo->id . '_details' => 'Currently on step 11 with sponsor',
            'field_' . $fDynamic->id => '__other__',
            'field_' . $fDynamic->id . '_other' => 'New Ad-hoc Convention Committee',
        ];

        $postResponse = $this->post(route('forms.submit.public', $form->slug), $submitData);
        $postResponse->assertStatus(200);
        $postResponse->assertSee($form->title);

        // 3. Verify submission data in database
        $this->assertEquals(1, $form->submissions()->count());
        $submission = $form->submissions()->first();

        $this->assertEquals(5, $submission->data[$fNumber->id]);
        
        $this->assertIsArray($submission->data[$fTable->id]);
        $this->assertCount(2, $submission->data[$fTable->id]);
        $this->assertEquals('GSR', $submission->data[$fTable->id][0]['Position']);
        $this->assertEquals('Literature Chair', $submission->data[$fTable->id][1]['Position']);

        $this->assertIsArray($submission->data[$fYesNo->id]);
        $this->assertEquals('yes', $submission->data[$fYesNo->id]['answer']);
        $this->assertEquals('Currently on step 11 with sponsor', $submission->data[$fYesNo->id]['details']);

        $this->assertEquals('New Ad-hoc Convention Committee', $submission->data[$fDynamic->id]);
    }

    public function test_can_view_report_and_export_csv_for_service_position_application()
    {
        $user = User::factory()->create();
        $user->assignRole('rsc');
        $this->actingAs($user);

        $form = CustomForm::create([
            'title' => 'Report Test Form',
            'type' => 'service_position_application',
            'status' => 'published',
            'user_id' => $user->id,
            'slug' => 'report-test-form',
        ]);

        $fTable = CustomFormField::create([
            'custom_form_id' => $form->id,
            'label' => 'Service Positions',
            'type' => 'table',
            'required' => true,
            'order' => 1,
            'options' => ['columns' => ['Position', 'Years']],
        ]);

        $fYesNo = CustomFormField::create([
            'custom_form_id' => $form->id,
            'label' => 'Active Sponsor?',
            'type' => 'yes_no_textbox',
            'required' => true,
            'order' => 2,
        ]);

        CustomFormSubmission::create([
            'custom_form_id' => $form->id,
            'data' => [
                $fTable->id => [
                    ['Position' => 'Treasurer', 'Years' => '2 years'],
                ],
                $fYesNo->id => [
                    'answer' => 'yes',
                    'details' => 'Regular weekly contact',
                ],
            ],
        ]);

        // View web report
        $response = $this->get(route('forms.report', $form->id));
        $response->assertStatus(200);
        $response->assertSee('Report Test Form');
        $response->assertSee('Treasurer');
        $response->assertSee('Regular weekly contact');

        // Export CSV
        $csvResponse = $this->get(route('forms.csv', $form->id));
        $csvResponse->assertStatus(200);
        $csvResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_can_create_and_view_form_with_select_attached_option_notes()
    {
        $user = User::factory()->create();
        $user->assignRole('rsc');
        $this->actingAs($user);

        // 1. Create form with select field having attached notes
        $response = $this->post(route('forms.store'), [
            'title' => 'Service Position Application with Notes',
            'type' => 'service_position_application',
            'status' => 'published',
            'fields' => [
                [
                    'label' => 'Desired Service Position',
                    'type' => 'select',
                    'options' => "Secretary: 2 years clean time required, Treasurer: 3 years clean time and accounting experience, General Member",
                    'required' => '1',
                ],
            ],
        ]);

        $response->assertRedirect(route('forms.index'));

        $form = CustomForm::where('title', 'Service Position Application with Notes')->first();
        $this->assertNotNull($form);
        $field = $form->fields()->where('type', 'select')->first();
        $this->assertNotNull($field);

        $this->assertEquals(['Secretary', 'Treasurer', 'General Member'], $field->options['choices']);
        $this->assertEquals([
            'Secretary' => '2 years clean time required',
            'Treasurer' => '3 years clean time and accounting experience',
        ], $field->options['option_notes']);

        // 2. View form publicly and assert data-note attributes are rendered
        $viewResponse = $this->get(route('forms.show.public', $form->slug));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('data-note="2 years clean time required"', false);
        $viewResponse->assertSee('data-note="3 years clean time and accounting experience"', false);
        $viewResponse->assertSee('select-note-container');

        // 3. Submit form with selected option
        $submitResponse = $this->post(route('forms.submit.public', $form->slug), [
            'field_' . $field->id => 'Secretary',
        ]);
        $submitResponse->assertStatus(200);

        $this->assertDatabaseHas('custom_form_submissions', [
            'custom_form_id' => $form->id,
        ]);
    }

    public function test_can_export_individual_submission_pdf_and_csv()
    {
        $user = User::factory()->create();
        $user->assignRole('rsc');
        $this->actingAs($user);

        $form = CustomForm::create([
            'title' => 'Single Export Test Form',
            'type' => 'service_position_application',
            'status' => 'published',
            'user_id' => $user->id,
            'slug' => 'single-export-test-form',
        ]);

        $fTable = CustomFormField::create([
            'custom_form_id' => $form->id,
            'label' => 'Service Positions',
            'type' => 'table',
            'required' => true,
            'order' => 1,
            'options' => ['columns' => ['Position', 'Years']],
        ]);

        $fYesNo = CustomFormField::create([
            'custom_form_id' => $form->id,
            'label' => 'Active Sponsor?',
            'type' => 'yes_no_textbox',
            'required' => true,
            'order' => 2,
        ]);

        $submission = CustomFormSubmission::create([
            'custom_form_id' => $form->id,
            'data' => [
                $fTable->id => [
                    ['Position' => 'Treasurer', 'Years' => '2 years'],
                ],
                $fYesNo->id => [
                    'answer' => 'yes',
                    'details' => 'Regular weekly contact',
                ],
            ],
        ]);

        // 1. Export Individual Response PDF
        $pdfResponse = $this->get(route('forms.submissionPdf', [$form->id, $submission->id]));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('content-type', 'application/pdf');

        // 2. Export Individual Response CSV
        $csvResponse = $this->get(route('forms.submissionCsv', [$form->id, $submission->id]));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('content-type'));

        $content = $csvResponse->streamedContent();
        $this->assertTrue(str_contains($content, __('messages.Question / Field')) || str_contains($content, 'Question / Field') || str_contains($content, 'السؤال / الحقل'));
        $this->assertTrue(str_contains($content, __('messages.Response / Answer')) || str_contains($content, 'Response / Answer') || str_contains($content, 'الاستجابة / الإجابة'));
        $this->assertStringContainsString('Service Positions', $content);
        $this->assertStringContainsString('Active Sponsor?', $content);
        $this->assertStringContainsString('Treasurer', $content);
        $this->assertStringContainsString('Regular weekly contact', $content);
    }

    public function test_form_update_preserves_field_ids_and_existing_submissions()
    {
        $user = User::factory()->create();
        $user->assignRole('rsc');
        $this->actingAs($user);

        // 1. Create a form with a field
        $form = CustomForm::create([
            'title' => 'Initial Title',
            'type' => 'service_position_application',
            'status' => 'published',
            'user_id' => $user->id,
            'slug' => 'initial-title-form',
        ]);

        $field = CustomFormField::create([
            'custom_form_id' => $form->id,
            'label' => 'Clean Time',
            'type' => 'text',
            'required' => true,
            'sort_order' => 0,
        ]);

        $initialFieldId = $field->id;

        // 2. Add a submission
        $submission = CustomFormSubmission::create([
            'custom_form_id' => $form->id,
            'data' => [
                $initialFieldId => '5 Years',
            ],
        ]);

        // 3. Update form title and edit field in-place
        $response = $this->put(route('forms.update', $form->id), [
            'title' => 'Updated New Title',
            'type' => 'service_position_application',
            'status' => 'published',
            'fields' => [
                [
                    'id' => $initialFieldId,
                    'label' => 'Clean Time (Updated Label)',
                    'type' => 'text',
                    'required' => '1',
                ],
            ],
        ]);

        $response->assertRedirect(route('forms.index'));

        // 4. Assert field ID did NOT change
        $form->refresh();
        $this->assertEquals('Updated New Title', $form->title);
        $this->assertEquals(1, $form->fields()->count());
        $this->assertEquals($initialFieldId, $form->fields()->first()->id);

        // 5. Assert submission data is still properly linked to the field
        $submission->refresh();
        $this->assertEquals('5 Years', $submission->data[$initialFieldId]);

        // 6. View report and verify submission data is displayed
        $reportResponse = $this->get(route('forms.report', $form->id));
        $reportResponse->assertStatus(200);
        $reportResponse->assertSee('5 Years');
    }

    public function test_urls_in_fixed_texts_and_descriptions_are_autolinked_with_target_blank()
    {
        $user = User::factory()->create();

        $form = CustomForm::create([
            'user_id' => $user->id,
            'title' => 'Form with Links',
            'type' => 'service_position_application',
            'status' => 'published',
        ]);

        CustomFormField::create([
            'custom_form_id' => $form->id,
            'label' => 'Please visit https://na-egypt.org/guidelines and www.na.org for more information.',
            'type' => 'static_text',
            'required' => false,
            'sort_order' => 0,
        ]);

        CustomFormField::create([
            'custom_form_id' => $form->id,
            'label' => 'Choose Position',
            'type' => 'select',
            'required' => true,
            'sort_order' => 1,
            'options' => [
                'choices' => [
                    'Chair: Read handbook at https://na-egypt.org/handbook before applying',
                ],
                'description' => 'For inquiries visit https://na-egypt.org/contact',
            ],
        ]);

        $response = $this->get(route('forms.show.public', $form->slug));
        $response->assertStatus(200);

        // Assert static text contains active links with target="_blank"
        $response->assertSee('href="https://na-egypt.org/guidelines"', false);
        $response->assertSee('href="https://www.na.org"', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);

        // Assert field description contains active link
        $response->assertSee('href="https://na-egypt.org/contact"', false);
    }
}
