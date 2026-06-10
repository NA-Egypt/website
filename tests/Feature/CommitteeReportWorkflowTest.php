<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceCommittee;
use App\Models\CommitteeReport;
use App\Models\CommitteeReportAttachment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommitteeReportWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        
        // Disable localization redirect middlewares to prevent redirects to /ar in test environment
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }

    protected function createCommittee($user, $attributes = [])
    {
        return ServiceCommittee::create(array_merge([
            'ar_name' => 'لجنة التجربة',
            'en_name' => 'Test Committee',
            'email' => $user->email,
            'user_id' => $user->id,
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'location' => 'https://maps.google.com',
            'chairman_name' => 'Name',
            'chairman_phone' => '12345678',
        ], $attributes));
    }

    public function test_unauthenticated_user_cannot_access_reports()
    {
        $this->get(route('committee-reports.index'))->assertRedirect();
        $this->get(route('committee-reports.archive'))->assertRedirect();
    }

    public function test_committee_user_can_create_and_update_draft()
    {
        $user = User::factory()->create(['email' => 'test-committee@naegypt.org']);
        $committee = $this->createCommittee($user);

        $this->actingAs($user);

        // 1. Create Draft
        $response = $this->post(route('committee-reports.store'), [
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday',
            'sections' => [
                ['headline' => 'Test Headline', 'content' => 'Draft body']
            ],
            'status' => 'draft',
            'is_exceptional' => '1',
            'attended_members' => 'John Doe, Jane Smith',
        ]);

        $response->assertRedirect(route('committee-reports.index'));
        $this->assertDatabaseHas('committee_reports', [
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-05-20',
            'status' => 'draft',
            'is_exceptional' => true,
            'report_date' => now()->toDateString(),
            'attended_members' => 'John Doe, Jane Smith',
            'body' => json_encode([['headline' => 'Test Headline', 'content' => 'Draft body']]),
        ]);

        $report = CommitteeReport::where('service_committee_id', $committee->id)->first();

        // 2. View Draft
        $this->get(route('committee-reports.show', $report->id))->assertStatus(200);

        // 3. Edit Draft
        $response = $this->put(route('committee-reports.update', $report->id), [
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday Updated',
            'sections' => [
                ['headline' => 'Test Headline Updated', 'content' => 'Draft body updated']
            ],
            'status' => 'draft',
            'is_exceptional' => '0',
            'attended_members' => 'John Doe, Jane Smith Updated',
        ]);

        $response->assertRedirect(route('committee-reports.index'));
        $this->assertDatabaseHas('committee_reports', [
            'id' => $report->id,
            'meeting_day_description' => 'Wednesday Updated',
            'status' => 'draft',
            'is_exceptional' => false,
            'attended_members' => 'John Doe, Jane Smith Updated',
            'body' => json_encode([['headline' => 'Test Headline Updated', 'content' => 'Draft body updated']]),
        ]);
    }

    public function test_unauthorized_user_cannot_view_or_edit_draft()
    {
        $user1 = User::factory()->create(['email' => 'comm1@naegypt.org']);
        $committee1 = $this->createCommittee($user1, [
            'ar_name' => 'لجنة 1',
            'en_name' => 'Committee 1',
        ]);

        $user2 = User::factory()->create(['email' => 'comm2@naegypt.org']);
        $committee2 = $this->createCommittee($user2, [
            'ar_name' => 'لجنة 2',
            'en_name' => 'Committee 2',
        ]);

        $report = CommitteeReport::create([
            'service_committee_id' => $committee1->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday',
            'body' => 'Private draft',
            'status' => 'draft',
        ]);

        // Log in as user2
        $this->actingAs($user2);

        // Should not see draft in index
        $response = $this->get(route('committee-reports.index'));
        $response->assertDontSee('Private draft');

        // Should get 403 on show/edit/update
        $this->get(route('committee-reports.show', $report->id))->assertStatus(403);
        $this->get(route('committee-reports.edit', $report->id))->assertStatus(403);
        $this->put(route('committee-reports.update', $report->id), [
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Hack',
            'status' => 'submitted',
        ])->assertStatus(403);
    }

    public function test_once_submitted_report_cannot_be_edited_by_committee()
    {
        $user = User::factory()->create(['email' => 'comm@naegypt.org']);
        $committee = $this->createCommittee($user);

        $report = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday',
            'body' => 'Submitted report',
            'status' => 'submitted',
        ]);

        $this->actingAs($user);

        $this->get(route('committee-reports.edit', $report->id))->assertRedirect(route('committee-reports.show', $report->id));
        $this->put(route('committee-reports.update', $report->id), [
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Hack',
            'status' => 'draft',
        ])->assertRedirect(route('committee-reports.show', $report->id));
    }

    public function test_rsc_cannot_see_drafts_but_can_see_submitted_reports()
    {
        $user = User::factory()->create(['email' => 'comm@naegypt.org']);
        $committee = $this->createCommittee($user);

        $draft = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday Draft',
            'body' => 'Draft body',
            'status' => 'draft',
        ]);

        $submitted = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday Submitted',
            'body' => 'Submitted body',
            'status' => 'submitted',
        ]);

        $rscUser = User::where('email', 'rsc@naegypt.org')->first() ?: User::factory()->create(['email' => 'rsc@naegypt.org']);
        $this->actingAs($rscUser);

        $response = $this->get(route('committee-reports.index'));
        $response->assertSee('Wednesday Submitted');
        $response->assertDontSee('Wednesday Draft');

        $this->get(route('committee-reports.show', $draft->id))->assertStatus(403);
        $this->get(route('committee-reports.show', $submitted->id))->assertStatus(200);
    }

    public function test_super_admin_can_see_and_access_draft_reports()
    {
        $user = User::factory()->create(['email' => 'comm@naegypt.org']);
        $committee = $this->createCommittee($user);

        $draft = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday Draft',
            'body' => 'Draft body',
            'status' => 'draft',
        ]);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super admin');
        $this->actingAs($superAdmin);

        $response = $this->get(route('committee-reports.index'));
        $response->assertSee('Wednesday Draft');

        $this->get(route('committee-reports.show', $draft->id))->assertStatus(200);
        $this->get(route('committee-reports.pdf', $draft->id))->assertStatus(200);

        // Verify export includes draft
        $response = $this->post(route('committee-reports.exportPdf'), [
            'report_ids' => [$draft->id],
        ]);
        $response->assertStatus(200);
    }

    public function test_committee_user_cannot_access_other_committee_drafts_but_can_access_submitted_and_approved()
    {
        $user1 = User::factory()->create(['email' => 'comm1@naegypt.org']);
        $committee1 = $this->createCommittee($user1, [
            'ar_name' => 'لجنة 1',
            'en_name' => 'Committee 1',
        ]);

        $user2 = User::factory()->create(['email' => 'comm2@naegypt.org']);
        $committee2 = $this->createCommittee($user2, [
            'ar_name' => 'لجنة 2',
            'en_name' => 'Committee 2',
        ]);

        $draft = CommitteeReport::create([
            'service_committee_id' => $committee1->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday Draft',
            'body' => 'Draft body',
            'status' => 'draft',
        ]);

        $submitted = CommitteeReport::create([
            'service_committee_id' => $committee1->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday Submitted',
            'body' => 'Submitted body',
            'status' => 'submitted',
        ]);

        // Log in as user2 (different committee)
        $this->actingAs($user2);

        // 1. Should not see other committee's draft in index
        $response = $this->get(route('committee-reports.index'));
        $response->assertDontSee('Wednesday Draft');

        // 2. Should get 403 on other committee's draft details/pdf/export
        $this->get(route('committee-reports.show', $draft->id))->assertStatus(403);
        $this->get(route('committee-reports.pdf', $draft->id))->assertStatus(403);

        // 3. Should be able to view and pdf other committee's submitted/approved reports (from the archive)
        $this->get(route('committee-reports.show', $submitted->id))->assertStatus(200);
        $this->get(route('committee-reports.pdf', $submitted->id))->assertStatus(200);

        // 4. Should be able to export other committee's submitted/approved reports
        $response = $this->post(route('committee-reports.exportPdf'), [
            'report_ids' => [$submitted->id],
        ]);
        $response->assertStatus(200);
    }

    public function test_archive_advanced_search_and_filtering()
    {
        $user = User::factory()->create(['email' => 'comm@naegypt.org']);
        $committee1 = $this->createCommittee($user, [
            'ar_name' => 'لجنة 1',
            'en_name' => 'Committee 1',
        ]);

        $user2 = User::factory()->create(['email' => 'comm2@naegypt.org']);
        $committee2 = $this->createCommittee($user2, [
            'ar_name' => 'لجنة 2',
            'en_name' => 'Committee 2',
        ]);

        // Report 1: Committee 1, Meeting Date 2026-05-20, Exceptional, Body: "Regular meeting details"
        $report1 = CommitteeReport::create([
            'service_committee_id' => $committee1->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday Regular',
            'body' => 'Regular meeting details',
            'status' => 'approved',
            'is_exceptional' => true,
        ]);

        // Report 2: Committee 2, Meeting Date 2026-06-10, Normal, Body: "Special topic review"
        $report2 = CommitteeReport::create([
            'service_committee_id' => $committee2->id,
            'meeting_date' => '2026-06-10',
            'meeting_day_description' => 'Wednesday Special',
            'body' => 'Special topic review',
            'status' => 'approved',
            'is_exceptional' => false,
        ]);

        $this->actingAs($user);

        // Test 1: Search by text ("Special")
        $response = $this->get(route('committee-reports.archive', ['search' => 'Special']));
        $response->assertStatus(200);
        $response->assertSee('Wednesday Special');
        $response->assertDontSee('Wednesday Regular');

        // Test 2: Filter by Committee 1
        $response = $this->get(route('committee-reports.archive', ['committee_id' => $committee1->id]));
        $response->assertStatus(200);
        $response->assertSee('Wednesday Regular');
        $response->assertDontSee('Wednesday Special');

        // Test 3: Filter by Date Range (2026-06-01 to 2026-06-30)
        $response = $this->get(route('committee-reports.archive', [
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30'
        ]));
        $response->assertStatus(200);
        $response->assertSee('Wednesday Special');
        $response->assertDontSee('Wednesday Regular');

        // Test 4: Filter by Exceptional Status
        $response = $this->get(route('committee-reports.archive', ['exceptional' => '1']));
        $response->assertStatus(200);
        $response->assertSee('Wednesday Regular');
        $response->assertDontSee('Wednesday Special');
    }

    public function test_file_attachments_upload_and_download()
    {
        $user = User::factory()->create(['email' => 'comm@naegypt.org']);
        $committee = $this->createCommittee($user);

        $this->actingAs($user);

        $file1 = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $file2 = UploadedFile::fake()->image('image.png');

        $this->post(route('committee-reports.store'), [
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday',
            'sections' => [
                ['headline' => null, 'content' => 'Report with attachments']
            ],
            'status' => 'draft',
            'attachments' => [$file1, $file2],
        ]);

        $report = CommitteeReport::where('service_committee_id', $committee->id)->first();
        $this->assertEquals(2, $report->attachments()->count());

        $attachment = $report->attachments()->first();
        Storage::disk('local')->assertExists($attachment->file_path);

        // Download attachment as report owner
        $response = $this->get(route('committee-reports.downloadAttachment', $attachment->id));
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=document.pdf');

        // Delete attachment from draft
        $this->delete(route('committee-reports.deleteAttachment', $attachment->id))
             ->assertRedirect();
        $this->assertEquals(1, $report->attachments()->count());
        Storage::disk('local')->assertMissing($attachment->file_path);
    }

    public function test_authenticated_user_can_access_archive()
    {
        $user = User::factory()->create(['email' => 'comm@naegypt.org']);
        $committee = $this->createCommittee($user);

        CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday Submitted',
            'body' => 'Submitted body',
            'status' => 'submitted',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('committee-reports.archive'));
        $response->assertStatus(200);
        $response->assertSee('2026');
        $response->assertSee('Wednesday Submitted');
    }

    public function test_rsc_can_approve_report()
    {
        $user = User::factory()->create(['email' => 'comm@naegypt.org']);
        $committee = $this->createCommittee($user);

        $report = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday Submitted',
            'body' => 'Submitted body',
            'status' => 'submitted',
        ]);

        $rscUser = User::where('email', 'rsc@naegypt.org')->first() ?: User::factory()->create(['email' => 'rsc@naegypt.org']);
        $this->actingAs($rscUser);

        $response = $this->post(route('committee-reports.approveAndSend', $report->id));

        $response->assertRedirect(route('committee-reports.index'));
        $this->assertDatabaseHas('committee_reports', [
            'id' => $report->id,
            'status' => 'approved',
            'review_notes' => null,
        ]);
    }

    public function test_rsc_can_return_to_draft_with_notes()
    {
        $user = User::factory()->create(['email' => 'comm@naegypt.org']);
        $committee = $this->createCommittee($user);

        $report = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday Submitted',
            'body' => 'Submitted body',
            'status' => 'submitted',
        ]);

        $rscUser = User::where('email', 'rsc@naegypt.org')->first() ?: User::factory()->create(['email' => 'rsc@naegypt.org']);
        $this->actingAs($rscUser);

        $response = $this->post(route('committee-reports.returnToDraft', $report->id), [
            'review_notes' => 'Please correct section 3 details.',
        ]);

        $response->assertRedirect(route('committee-reports.index'));
        $this->assertDatabaseHas('committee_reports', [
            'id' => $report->id,
            'status' => 'draft',
            'review_notes' => 'Please correct section 3 details.',
        ]);
    }

    public function test_non_rsc_cannot_approve_or_return_report()
    {
        $user = User::factory()->create(['email' => 'comm@naegypt.org']);
        $committee = $this->createCommittee($user);

        $report = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday Submitted',
            'body' => 'Submitted body',
            'status' => 'submitted',
        ]);

        $this->actingAs($user);

        // Try to approve
        $this->post(route('committee-reports.approveAndSend', $report->id))
             ->assertStatus(403);

        // Try to return to draft
        $this->post(route('committee-reports.returnToDraft', $report->id), [
            'review_notes' => 'Hack notes',
        ])->assertStatus(403);
    }

    public function test_servicebody_report_availability_after_tenth_of_month()
    {
        $user = User::factory()->create(['email' => 'comm@naegypt.org']);
        $committee = $this->createCommittee($user);

        // Current month report
        $currentMonthDate = now()->format('Y-m-d');
        $approvedReportCurrentMonth = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => $currentMonthDate,
            'meeting_day_description' => 'Current Month Report',
            'body' => 'Details',
            'status' => 'approved',
        ]);

        // Previous month report
        $previousMonthDate = now()->subMonth()->format('Y-m-d');
        $approvedReportPreviousMonth = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => $previousMonthDate,
            'meeting_day_description' => 'Previous Month Report',
            'body' => 'Details',
            'status' => 'approved',
        ]);

        // Login as ServiceBody user
        $serviceBodyUser = User::factory()->create();
        $serviceBodyUser->assignRole('ServiceBody');
        $this->actingAs($serviceBodyUser);

        // Previous month report is always visible in index, archive & detail view
        $response = $this->get(route('committee-reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Previous Month Report');

        $responseArchive = $this->get(route('committee-reports.archive'));
        $responseArchive->assertSee('Previous Month Report');
        $this->get(route('committee-reports.show', $approvedReportPreviousMonth->id))->assertStatus(200);

        // Current month report availability depends on current day of month
        if (now()->day < 10) {
            $response->assertDontSee('Current Month Report');
            $responseArchive->assertDontSee('Current Month Report');
            $this->get(route('committee-reports.show', $approvedReportCurrentMonth->id))->assertStatus(403);
        } else {
            $response->assertSee('Current Month Report');
            $responseArchive->assertSee('Current Month Report');
            $this->get(route('committee-reports.show', $approvedReportCurrentMonth->id))->assertStatus(200);
        }
    }
    public function test_service_committee_logo_and_footers()
    {
        Storage::fake('public');
        $user = User::factory()->create(['email' => 'comm@naegypt.org']);
        $committee = $this->createCommittee($user);

        // Assign super admin to update committee details (logo & default footer)
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super admin');
        $this->actingAs($superAdmin);

        $logoFile = UploadedFile::fake()->image('custom_logo.png');

        $response = $this->put(route('serviceCommittee.update', $committee->id), [
            'ar_name' => 'لجنة معدلة',
            'en_name' => 'Modified Committee',
            'email' => $user->email,
            'chairman_name' => 'Chairman Name',
            'logo' => $logoFile,
            'default_footer' => 'This is the default committee footer text.',
        ]);

        $response->assertRedirect(route('serviceCommittee.index'));
        
        $committee->refresh();
        $this->assertNotNull($committee->logo);
        Storage::disk('public')->assertExists($committee->logo);
        $this->assertEquals('This is the default committee footer text.', $committee->default_footer);

        // Now test report footer override
        $this->actingAs($user);

        // 1. Report without custom footer - should fallback to default_footer in show view and PDF
        $report1 = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday',
            'body' => 'Report body 1',
            'status' => 'approved',
        ]);

        $response1 = $this->get(route('committee-reports.show', $report1->id));
        $response1->assertSee('This is the default committee footer text.');

        // 2. Report with custom footer - should override default_footer
        $report2 = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-05-21',
            'meeting_day_description' => 'Thursday',
            'body' => 'Report body 2',
            'status' => 'approved',
            'footer' => 'Overridden custom report footer.',
        ]);

        $response2 = $this->get(route('committee-reports.show', $report2->id));
        $response2->assertSee('Overridden custom report footer.');
        $response2->assertDontSee('This is the default committee footer text.');
    }
}
