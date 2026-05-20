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
            'body' => 'Draft body',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('committee-reports.index'));
        $this->assertDatabaseHas('committee_reports', [
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-05-20',
            'status' => 'draft',
        ]);

        $report = CommitteeReport::where('service_committee_id', $committee->id)->first();

        // 2. View Draft
        $this->get(route('committee-reports.show', $report->id))->assertStatus(200);

        // 3. Edit Draft
        $response = $this->put(route('committee-reports.update', $report->id), [
            'meeting_date' => '2026-05-20',
            'meeting_day_description' => 'Wednesday Updated',
            'body' => 'Draft body updated',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('committee-reports.index'));
        $this->assertDatabaseHas('committee_reports', [
            'id' => $report->id,
            'meeting_day_description' => 'Wednesday Updated',
            'status' => 'draft',
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

        $rscUser = User::factory()->create(['email' => 'rsc@naegypt.org']);
        $this->actingAs($rscUser);

        $response = $this->get(route('committee-reports.index'));
        $response->assertSee('Wednesday Submitted');
        $response->assertDontSee('Wednesday Draft');

        $this->get(route('committee-reports.show', $draft->id))->assertStatus(403);
        $this->get(route('committee-reports.show', $submitted->id))->assertStatus(200);
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
            'body' => 'Report with attachments',
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
}
