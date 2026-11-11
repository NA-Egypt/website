<?php

namespace Tests\Feature;

use App\Jobs\SendCommitteeReportSubmissionNotification;
use App\Models\CommitteeReport;
use App\Models\ServiceCommittee;
use App\Models\User;
use App\Services\WhatsApp\WhatsAppClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CommitteeReportSuperAdminAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);
    }

    public function test_super_admin_can_view_committee_and_workgroup_drafts_across_tabs(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super admin');
        $this->actingAs($superAdmin);

        // Parent committee & draft
        $committee = ServiceCommittee::create([
            'ar_name' => 'لجنة العلاقات العامة',
            'en_name' => 'Public Relations Committee',
            'status' => 'active',
        ]);
        $committeeDraft = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => now()->toDateString(),
            'meeting_day_description' => 'Tuesday PR Draft',
            'body' => 'PR draft content',
            'status' => 'draft',
        ]);

        // Child workgroup & draft
        $workgroup = ServiceCommittee::create([
            'parent_id' => $committee->id,
            'ar_name' => 'مجموعة عمل التواصل الاجتماعي',
            'en_name' => 'Social Media Workgroup',
            'status' => 'active',
        ]);
        $workgroupDraft = CommitteeReport::create([
            'service_committee_id' => $workgroup->id,
            'meeting_date' => now()->toDateString(),
            'meeting_day_description' => 'Social Media Draft',
            'body' => 'Social media draft content',
            'status' => 'draft',
        ]);

        // 1. Committee tab should show committee draft, but not standalone workgroup draft
        $responseCommitteeTab = $this->get(route('committee-reports.index', ['tab' => 'committee']));
        $responseCommitteeTab->assertStatus(200);
        $responseCommitteeTab->assertSee('Tuesday PR Draft');
        $responseCommitteeTab->assertDontSee('Social Media Draft');

        // 2. Workgroups tab should show workgroup draft, but not parent committee draft
        $responseWorkgroupTab = $this->get(route('committee-reports.index', ['tab' => 'workgroups']));
        $responseWorkgroupTab->assertStatus(200);
        $responseWorkgroupTab->assertSee('Social Media Draft');
        $responseWorkgroupTab->assertDontSee('Tuesday PR Draft');
    }

    public function test_super_admin_status_filtering_works_correctly(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super admin');
        $this->actingAs($superAdmin);

        $committee = ServiceCommittee::create([
            'ar_name' => 'لجنة الأنشطة',
            'en_name' => 'Activities Committee',
            'status' => 'active',
        ]);

        $draft = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => now()->toDateString(),
            'meeting_day_description' => 'Draft Meeting',
            'body' => 'Draft content',
            'status' => 'draft',
        ]);

        $submitted = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => now()->toDateString(),
            'meeting_day_description' => 'Submitted Meeting',
            'body' => 'Submitted content',
            'status' => 'submitted',
        ]);

        // Filter status=draft: see draft, don't see submitted
        $responseDraft = $this->get(route('committee-reports.index', ['tab' => 'committee', 'status' => 'draft']));
        $responseDraft->assertSee('Draft Meeting');
        $responseDraft->assertDontSee('Submitted Meeting');

        // Filter status=submitted: see submitted, don't see draft
        $responseSubmitted = $this->get(route('committee-reports.index', ['tab' => 'committee', 'status' => 'submitted']));
        $responseSubmitted->assertSee('Submitted Meeting');
        $responseSubmitted->assertDontSee('Draft Meeting');
    }

    public function test_submitting_report_via_web_send_dispatches_notification_job(): void
    {
        Queue::fake();

        $user = User::factory()->create(['email' => 'h_i@naegypt.org']);
        $committee = ServiceCommittee::create([
            'user_id' => $user->id,
            'email' => 'h_i@naegypt.org',
            'ar_name' => 'لجنة المستشفيات والمؤسسات',
            'en_name' => 'H&I Committee',
            'status' => 'active',
        ]);

        $report = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => now()->toDateString(),
            'meeting_day_description' => 'H&I Meeting',
            'body' => 'Draft details',
            'status' => 'draft',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('committee-reports.send', $report->id));
        $response->assertSessionHas('success');

        $this->assertEquals('submitted', $report->fresh()->status);

        Queue::assertPushed(SendCommitteeReportSubmissionNotification::class, function ($job) use ($report) {
            return $job->reportId === $report->id;
        });
    }

    public function test_notification_job_sends_email_to_rsc_and_altrsc(): void
    {
        Mail::fake();

        $committee = ServiceCommittee::create([
            'ar_name' => 'لجنة الأدبيات',
            'en_name' => 'Literature Committee',
            'status' => 'active',
        ]);

        $report = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-11-10',
            'meeting_day_description' => 'Tuesday Lit Meeting',
            'body' => 'Literature report',
            'status' => 'submitted',
        ]);

        // Run the job handler with Mail fake
        $job = new SendCommitteeReportSubmissionNotification($report->id);
        $job->handle();

        Mail::assertSent(\App\Mail\CommitteeReportSubmittedMail::class, function ($mail) {
            return $mail->hasTo('rsc@naegypt.org')
                && $mail->hasTo('altrsc@naegypt.org')
                && $mail->hasTo('arsc@naegypt.org');
        });
    }

    public function test_notification_job_bypasses_whatsapp_when_no_numbers_configured_without_fallback_to_committee_phone(): void
    {
        Mail::fake();

        // Committee 83 with chairman phone
        $rscCommittee = ServiceCommittee::updateOrCreate(
            ['id' => 83],
            [
                'ar_name' => 'لجنة خدمة إقليم مصر',
                'en_name' => 'Egypt Regional Service Committee',
                'email' => 'RSC@naegypt.org',
                'chairman_name' => 'Chairman Name',
                'chairman_phone' => '01222722722',
            ]
        );

        $committee = ServiceCommittee::create([
            'ar_name' => 'لجنة الترجمة',
            'en_name' => 'Translation Committee',
            'status' => 'active',
        ]);

        $report = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-11-10',
            'meeting_day_description' => 'Translation Meeting',
            'body' => 'Translation report',
            'status' => 'submitted',
        ]);

        // WhatsApp numbers strictly empty
        Config::set('whatsapp.rsc_notification_numbers', []);

        // Mock WhatsAppClient: it should NEVER be called when numbers are not configured
        $mockClient = $this->createMock(WhatsAppClient::class);
        $mockClient->expects($this->never())->method('sendTextMessage');
        $this->app->instance(WhatsAppClient::class, $mockClient);

        $job = new SendCommitteeReportSubmissionNotification($report->id);
        $job->handle();

        // Email is still sent
        Mail::assertSent(\App\Mail\CommitteeReportSubmittedMail::class, function ($mail) {
            return $mail->hasTo('rsc@naegypt.org')
                && $mail->hasTo('altrsc@naegypt.org');
        });
    }

    public function test_notification_job_sends_whatsapp_when_numbers_configured(): void
    {
        Mail::fake();

        $committee = ServiceCommittee::create([
            'ar_name' => 'لجنة الخط الساخن',
            'en_name' => 'Helpline Committee',
            'status' => 'active',
        ]);

        $report = CommitteeReport::create([
            'service_committee_id' => $committee->id,
            'meeting_date' => '2026-11-10',
            'meeting_day_description' => 'Helpline Meeting',
            'body' => 'Helpline report',
            'status' => 'submitted',
        ]);

        $configuredNumber = '201099998888';
        Config::set('whatsapp.rsc_notification_numbers', [$configuredNumber]);

        $mockClient = $this->createMock(WhatsAppClient::class);
        $mockClient->expects($this->once())
            ->method('sendTextMessage')
            ->with(
                $this->equalTo($configuredNumber),
                $this->stringContains('إشعار تقديم تقرير لجنة')
            )
            ->willReturn(['success' => true]);

        $this->app->instance(WhatsAppClient::class, $mockClient);

        $job = new SendCommitteeReportSubmissionNotification($report->id);
        $job->handle();
    }
}
