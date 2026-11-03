<?php

namespace Tests\Feature;

use App\Models\CommitteeReport;
use App\Models\HelplineCall;
use App\Models\HelplineVolunteer;
use App\Models\ServiceCommittee;
use App\Models\User;
use App\Services\HelplineReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HelplineCallTest extends TestCase
{
    use RefreshDatabase;

    protected User $phoneUser;
    protected User $prUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        $perm = Permission::firstOrCreate([
            'name' => 'manage helpline',
            'guard_name' => 'web',
        ]);

        Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $phonelineRole = Role::firstOrCreate(['name' => 'Phoneline', 'guard_name' => 'web']);
        $phonelineRole->givePermissionTo($perm);

        $this->phoneUser = User::factory()->create(['email' => 'phone@naegypt.org']);
        $this->phoneUser->givePermissionTo($perm);

        $this->prUser = User::factory()->create(['email' => 'pr@naegypt.org']);
        $this->prUser->givePermissionTo($perm);

        $this->regularUser = User::factory()->create(['email' => 'member@naegypt.org']);
    }

    public function test_public_form_can_be_rendered()
    {
        $response = $this->get('/forms/helpline');

        $response->assertStatus(200);
        $response->assertSee('تسجيل استجابة مكالمة خط المساعدة');
        $response->assertSee('أقل من 5 دقائق');
        $response->assertSee('10:00 AM - 12:00 PM');
        $response->assertSee('8:00 PM - 10:00 PM');
        // Search engine exclusion checks
        $response->assertSee('name="robots" content="noindex, nofollow', false);
        $this->assertStringContainsString('noindex', $response->headers->get('X-Robots-Tag'));
        // Mobile viewport check
        $response->assertSee('name="viewport" content="width=device-width', false);
    }

    public function test_public_form_submission_stores_call_with_entry_time()
    {
        $volunteer = HelplineVolunteer::firstOrCreate(
            ['name' => 'أحمد ع.'],
            ['is_active' => true, 'sort_order' => 1]
        );

        $payload = [
            'duration' => 'less_than_5',
            'call_date' => Carbon::today()->format('Y-m-d'),
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'أعضاء محتملة',
            'referral_source' => 'الموقع الالكتروني',
            'volunteer_name' => $volunteer->name,
            'is_step_12' => '0',
            'call_brief' => 'استفسار عن موعد أقرب اجتماع في منطقة المعادي.',
            'discuss_in_meeting' => '0',
            'additional_info' => 'تم توجيهه للاجتماع بنجاح',
        ];

        $response = $this->postJson('/forms/helpline', $payload);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('helpline_calls', [
            'duration' => 'less_than_5',
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'أعضاء محتملة',
            'volunteer_name' => $volunteer->name,
            'is_step_12' => 0,
            'discuss_in_meeting' => 0,
        ]);

        $call = HelplineCall::latest('id')->first();
        $this->assertNotNull($call->entry_time);
    }

    public function test_public_form_validates_conditional_other_fields()
    {
        $payload = [
            'duration' => 'more_than_5',
            'call_date' => Carbon::today()->format('Y-m-d'),
            'call_time_shift' => '2:00 PM - 4:00 PM',
            'caller_type' => 'أخرى',
            'caller_type_other' => '', // empty other!
            'referral_source' => 'أخرى',
            'referral_source_other' => '', // empty other!
            'volunteer_name' => 'أخرى',
            'volunteer_name_other' => '', // empty other!
            'is_step_12' => '1',
            'call_brief' => 'مكالمة طويلة',
            'discuss_in_meeting' => '1',
        ];

        $response = $this->post('/forms/helpline', $payload);
        $response->assertSessionHasErrors(['caller_type_other']);
    }

    public function test_public_form_submission_allows_empty_call_brief_for_existing_member()
    {
        $payload = [
            'duration' => 'less_than_5',
            'call_date' => Carbon::today()->format('Y-m-d'),
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'عضو حالي',
            'referral_source' => 'الموقع الالكتروني',
            'volunteer_name' => 'أحمد ع.',
            'is_step_12' => '0',
            'call_brief' => '',
            'discuss_in_meeting' => '0',
        ];

        $response = $this->postJson('/forms/helpline', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('helpline_calls', [
            'caller_type' => 'عضو حالي',
            'call_brief' => null,
        ]);
    }

    public function test_public_form_submission_allows_empty_call_brief_for_wrong_call()
    {
        $payload = [
            'duration' => 'less_than_5',
            'call_date' => Carbon::today()->format('Y-m-d'),
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'مكالمة بالخطأ',
            'referral_source' => 'أخرى',
            'referral_source_other' => 'رقم خطأ',
            'volunteer_name' => 'أحمد ع.',
            'is_step_12' => '0',
            'call_brief' => '',
            'discuss_in_meeting' => '0',
        ];

        $response = $this->postJson('/forms/helpline', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('helpline_calls', [
            'caller_type' => 'مكالمة بالخطأ',
            'call_brief' => null,
        ]);
    }

    public function test_public_form_submission_validates_conditional_hospital_name()
    {
        $payload = [
            'duration' => 'less_than_5',
            'call_date' => Carbon::today()->format('Y-m-d'),
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'أعضاء محتملة',
            'referral_source' => 'لجنة المستشفيات',
            'hospital_name' => '',
            'volunteer_name' => 'أحمد ع.',
            'is_step_12' => '0',
            'call_brief' => 'استفسار من مستشفى',
            'discuss_in_meeting' => '0',
        ];

        $response = $this->post('/forms/helpline', $payload);
        $response->assertSessionHasErrors(['hospital_name']);
    }

    public function test_public_form_submission_validates_conditional_poster_location()
    {
        $payload = [
            'duration' => 'less_than_5',
            'call_date' => Carbon::today()->format('Y-m-d'),
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'أعضاء محتملة',
            'referral_source' => 'ملصقات الزمالة',
            'poster_location' => '',
            'volunteer_name' => 'أحمد ع.',
            'is_step_12' => '0',
            'call_brief' => 'استفسار عن ملصق',
            'discuss_in_meeting' => '0',
        ];

        $response = $this->post('/forms/helpline', $payload);
        $response->assertSessionHasErrors(['poster_location']);
    }

    public function test_public_form_submission_stores_hospital_and_poster_location()
    {
        $payload = [
            'duration' => 'more_than_5',
            'call_date' => Carbon::today()->format('Y-m-d'),
            'call_time_shift' => '2:00 PM - 4:00 PM',
            'caller_type' => 'أعضاء محتملة',
            'referral_source' => 'لجنة المستشفيات',
            'hospital_name' => 'مستشفى الدمرداش',
            'volunteer_name' => 'أحمد ع.',
            'is_step_12' => '1',
            'call_brief' => 'مكالمة من عيادة الإدمان بالمستشفى.',
            'discuss_in_meeting' => '0',
        ];

        $response = $this->postJson('/forms/helpline', $payload);
        $response->assertStatus(201);

        $this->assertDatabaseHas('helpline_calls', [
            'referral_source' => 'لجنة المستشفيات',
            'hospital_name' => 'مستشفى الدمرداش',
        ]);
    }

    public function test_public_api_schema_endpoint()
    {
        $response = $this->getJson('/api/v1/helpline-calls/schema');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'title',
            'shifts',
            'caller_types',
            'referral_sources',
            'durations',
            'volunteers',
            'fields_order',
        ]);
        $this->assertContains('8:00 PM - 10:00 PM', $response->json('shifts'));
    }

    public function test_public_api_submission_endpoint()
    {
        $payload = [
            'duration' => 'more_than_5',
            'call_date' => Carbon::today()->format('Y-m-d'),
            'call_time_shift' => '8:00 PM - 10:00 PM',
            'caller_type' => 'عضو حالي',
            'referral_source' => 'جدول الاجتماعات',
            'volunteer_name' => 'محمد م.',
            'is_step_12' => true,
            'call_brief' => 'طلب مساعدة هاتفية لخطوة 12.',
            'discuss_in_meeting' => false,
            'additional_info' => 'تم الاتصال به من قبل عضو متعافي',
        ];

        $response = $this->postJson('/api/v1/helpline-calls', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.duration', 'more_than_5');
        $response->assertJsonPath('data.call_time_shift', '8:00 PM - 10:00 PM');
        $response->assertJsonPath('data.is_step_12', true);
        $this->assertDatabaseHas('helpline_calls', [
            'call_time_shift' => '8:00 PM - 10:00 PM',
        ]);
    }

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get('/helpline');
        $response->assertRedirect('/');
    }

    public function test_regular_user_cannot_access_dashboard()
    {
        $response = $this->actingAs($this->regularUser)->get('/helpline');
        $response->assertStatus(403);
    }

    public function test_phone_user_can_access_dashboard_and_export_excel()
    {
        $response = $this->actingAs($this->phoneUser)->get('/helpline');
        $response->assertStatus(200);
        $response->assertSee('إدارة تقارير واستجابات خط المساعدة');

        $exportResponse = $this->actingAs($this->phoneUser)->get('/helpline/export/excel');
        $exportResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $exportResponse->headers->get('Content-Type'));
    }

    public function test_phone_user_can_export_pdf_report()
    {
        $exportPdfResponse = $this->actingAs($this->phoneUser)->get('/helpline/export/pdf');
        $exportPdfResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $exportPdfResponse->headers->get('Content-Type'));
    }

    public function test_pr_user_can_access_dashboard_and_manage_volunteers()
    {
        $response = $this->actingAs($this->prUser)->get('/helpline/volunteers');
        $response->assertStatus(200);
        $response->assertSee('قائمة متطوعي خطوط المساعدة');

        // Add volunteer
        $storeResponse = $this->actingAs($this->prUser)->post('/helpline/volunteers', [
            'name' => 'متطوع تجريبي جديد ' . rand(1000, 9999),
            'phone' => '01012345678',
            'sort_order' => 10,
            'is_active' => '1',
        ]);
        $storeResponse->assertRedirect('/helpline/volunteers');
    }

    public function test_sync_to_workgroup_report_for_pr_committee_embedding()
    {
        // Ensure Helpline Workgroup committee exists
        $workgroup = ServiceCommittee::where('email', 'phone@naegypt.org')->first()
            ?? ServiceCommittee::find(85);

        if (!$workgroup) {
            $workgroup = ServiceCommittee::create([
                'id' => 85,
                'parent_id' => 1,
                'status' => 'active',
                'ar_name' => 'مجموعة خطوط المساعدة',
                'en_name' => 'Helplines Workgroup',
                'email' => 'phone@naegypt.org',
            ]);
        }

        $response = $this->actingAs($this->phoneUser)->post('/helpline/sync-workgroup');
        $response->assertSessionHas('success');

        // Verify draft report was created
        $report = CommitteeReport::where('service_committee_id', $workgroup->id)
            ->where('status', 'draft')
            ->latest('id')
            ->first();

        $this->assertNotNull($report);
        $this->assertStringContainsString('تقرير مكالمات خط المساعدة', $report->body);
    }

    public function test_first_tuesday_monthly_cycle_calculation()
    {
        $service = new HelplineReportService();

        // 1st Tuesday of September 2026:
        // Sep 1, 2026 was a Tuesday!
        $sepFirstTuesday = $service->getFirstTuesdayOfMonth(2026, 9);
        $this->assertEquals(Carbon::TUESDAY, $sepFirstTuesday->dayOfWeek);
        $this->assertEquals(1, $sepFirstTuesday->day);
        $this->assertEquals('23:59:59', $sepFirstTuesday->format('H:i:s'));

        // Cycle test when now is after the 1st Tuesday of September:
        // e.g. Sep 16, 2026: Cycle starts on Sep 1 00:00:00, and ends at Monday Oct 5 23:59:59 (before 1st Tuesday of Oct)
        $now = Carbon::create(2026, 9, 16, 12, 0, 0);
        $cycle = $service->getCurrentCycleWindow($now);

        $this->assertEquals('2026-09-01 00:00:00', $cycle['start']->format('Y-m-d H:i:s'));
        $this->assertEquals(Carbon::MONDAY, $cycle['end']->dayOfWeek);
        $this->assertEquals(10, $cycle['end']->month);
        $this->assertEquals(5, $cycle['end']->day);
    }

    public function test_helpline_vue_data_endpoint_returns_json()
    {
        $response = $this->actingAs($this->phoneUser)->getJson('/helpline/data');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'calls' => ['data', 'total', 'current_page'],
            'reportData' => ['total_calls', 'duration_less_than_5', 'duration_more_than_5'],
            'period' => ['type', 'cycle_key', 'label'],
        ]);
    }

    public function test_toggle_discuss_in_meeting_action()
    {
        $call = HelplineCall::first();
        if (!$call) {
            $call = HelplineCall::create([
                'duration' => 'less_than_5',
                'call_date' => Carbon::now()->toDateString(),
                'call_time_shift' => '12:00 م - 04:00 م',
                'caller_type' => 'مدمن يبحث عن تعافي',
                'referral_source' => 'موقع NA الرسمي',
                'volunteer_name' => 'أحمد ع.',
                'is_step_12' => false,
                'call_brief' => 'مكالمة اختبار للتبديل',
                'discuss_in_meeting' => false,
                'entry_time' => Carbon::now(),
            ]);
        }
        $initialState = (bool) $call->discuss_in_meeting;

        $response = $this->actingAs($this->phoneUser)->postJson("/helpline/calls/{$call->id}/toggle-discuss");
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'discuss_in_meeting' => !$initialState,
        ]);

        $this->assertEquals(!$initialState, $call->fresh()->discuss_in_meeting);
    }

    public function test_destroy_helpline_call()
    {
        $call = HelplineCall::create([
            'duration' => 'less_than_5',
            'call_date' => Carbon::now()->toDateString(),
            'call_time_shift' => '12:00 م - 04:00 م',
            'caller_type' => 'مدمن يبحث عن تعافي',
            'referral_source' => 'موقع NA الرسمي',
            'volunteer_name' => 'أحمد ع.',
            'is_step_12' => false,
            'call_brief' => 'مكالمة اختبار للحذف',
            'discuss_in_meeting' => false,
            'entry_time' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->prUser)->deleteJson("/helpline/calls/{$call->id}");
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertNull(HelplineCall::find($call->id));
    }

    public function test_third_tuesday_and_meeting_cycle_calculation()
    {
        $service = new HelplineReportService();

        // September 2026: 1st Tuesday is Sep 1, 3rd Tuesday is Sep 15
        $sep1st = $service->getFirstTuesdayOfMonth(2026, 9);
        $sep3rd = $service->getThirdTuesdayOfMonth(2026, 9);

        $this->assertEquals(Carbon::TUESDAY, $sep3rd->dayOfWeek);
        $this->assertEquals(15, $sep3rd->day);
        $this->assertEquals('23:59:59', $sep3rd->format('H:i:s'));

        // October 2026: 1st Tuesday is Oct 6, 3rd Tuesday is Oct 20
        $oct3rd = $service->getThirdTuesdayOfMonth(2026, 10);
        $this->assertEquals(20, $oct3rd->day);

        // Case 1: Date is before 1st Tuesday of September 2026 (e.g. Aug 31, Monday before Sep 1)
        // Cycle must end on Aug 31 (Monday), and start on August 3rd Tuesday (Aug 18)
        $now1 = Carbon::create(2026, 8, 31, 14, 0, 0);
        $meetingCycle1 = $service->getCurrentMeetingCycleWindow($now1);
        $this->assertEquals('2026-08-31 23:59:59', $meetingCycle1['end']->format('Y-m-d H:i:s'));
        $this->assertEquals('2026-08-18 00:00:00', $meetingCycle1['start']->format('Y-m-d H:i:s'));
        $this->assertEquals('first_tuesday', $meetingCycle1['type']);
        $this->assertEquals('meeting', $meetingCycle1['group']);

        // Case 2: Date is between 1st and 3rd Tuesday of September 2026 (e.g. Sep 8)
        // Cycle must end on Monday before 3rd Tuesday (Sep 14), and start on 1st Tuesday (Sep 1)
        $now2 = Carbon::create(2026, 9, 8, 12, 0, 0);
        $meetingCycle2 = $service->getCurrentMeetingCycleWindow($now2);
        $this->assertEquals('2026-09-14 23:59:59', $meetingCycle2['end']->format('Y-m-d H:i:s'));
        $this->assertEquals('2026-09-01 00:00:00', $meetingCycle2['start']->format('Y-m-d H:i:s'));
        $this->assertEquals('third_tuesday', $meetingCycle2['type']);

        // Case 3: Date is on or after 3rd Tuesday of September 2026 (e.g. Sep 20)
        // Cycle must start on 3rd Tuesday of September (Sep 15) and end on Monday before 1st Tuesday of October (Oct 5)
        $now3 = Carbon::create(2026, 9, 20, 10, 0, 0);
        $meetingCycle3 = $service->getCurrentMeetingCycleWindow($now3);
        $this->assertEquals('2026-10-05 23:59:59', $meetingCycle3['end']->format('Y-m-d H:i:s'));
        $this->assertEquals('2026-09-15 00:00:00', $meetingCycle3['start']->format('Y-m-d H:i:s'));
        $this->assertEquals('first_tuesday', $meetingCycle3['type']);
    }

    public function test_empty_past_cycles_are_filtered_and_only_current_cycle_kept()
    {
        $service = new HelplineReportService();

        // When only monthly cycles are requested, without calls only 1 current monthly cycle is returned
        $monthlyCycles = $service->getAvailableCycles(12, true, 'monthly');
        $this->assertCount(1, $monthlyCycles);
        $currentMonthly = array_values($monthlyCycles)[0];
        $this->assertTrue($currentMonthly['is_current']);
        $this->assertEquals('monthly', $currentMonthly['group']);

        // When all cycles are requested, without calls only active meeting cycle and active monthly cycle are returned
        $allCycles = $service->getAvailableCycles(12, true, 'all');
        $this->assertCount(2, $allCycles);

        $hasMeeting = false;
        $hasMonthly = false;
        foreach ($allCycles as $c) {
            if ($c['group'] === 'meeting') $hasMeeting = true;
            if ($c['group'] === 'monthly') $hasMonthly = true;
            $this->assertTrue($c['is_current']);
        }
        $this->assertTrue($hasMeeting);
        $this->assertTrue($hasMonthly);
    }

    public function test_public_form_rejects_future_call_date()
    {
        $volunteer = HelplineVolunteer::create(['name' => 'أحمد ع.', 'is_active' => true]);
        $futureDate = Carbon::tomorrow()->toDateString();
        $payload = [
            'duration' => 'less_than_5',
            'call_date' => $futureDate,
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'أعضاء محتملة',
            'referral_source' => 'الموقع الالكتروني',
            'volunteer_name' => $volunteer->name,
            'is_step_12' => '0',
            'call_brief' => 'مكالمة اختبار لتاريخ مستقبلي',
            'discuss_in_meeting' => '0',
        ];

        $response = $this->postJson('/forms/helpline', $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['call_date']);
    }

    public function test_call_date_serializes_without_utc_day_shifting()
    {
        $call = HelplineCall::create([
            'duration' => 'less_than_5',
            'call_date' => '2026-09-21',
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'أعضاء محتملة',
            'referral_source' => 'الموقع الالكتروني',
            'volunteer_name' => 'أحمد ع.',
            'is_step_12' => false,
            'call_brief' => 'اختبار عدم ترحيل التاريخ في JSON',
            'discuss_in_meeting' => false,
            'entry_time' => Carbon::now(),
        ]);

        $array = $call->toArray();
        $this->assertEquals('2026-09-21', $array['call_date']);
    }

    public function test_report_service_filters_by_call_date()
    {
        // Call that took place on 2026-08-15 (previous cycle) but entered today
        $pastCall = HelplineCall::create([
            'duration' => 'less_than_5',
            'call_date' => '2026-08-15',
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'أعضاء محتملة',
            'referral_source' => 'الموقع الالكتروني',
            'volunteer_name' => 'أحمد ع.',
            'is_step_12' => false,
            'call_brief' => 'مكالمة سابقة بتسجيل اليوم',
            'discuss_in_meeting' => false,
            'entry_time' => Carbon::now(),
        ]);

        $service = new HelplineReportService();
        $reportData = $service->getReportData(Carbon::parse('2026-08-01'), Carbon::parse('2026-08-31'));

        $this->assertGreaterThanOrEqual(1, $reportData['total_calls']);
        $this->assertTrue($reportData['calls']->pluck('id')->contains($pastCall->id));
    }

    public function test_meeting_cycle_filters_calls_between_third_tuesday_and_first_tuesday()
    {
        // Window: 2026-09-15 (3rd Tuesday Sep 15) to 2026-10-05 (Monday before 1st Tuesday Oct)
        $insideCall = HelplineCall::create([
            'duration' => 'less_than_5',
            'call_date' => '2026-09-20',
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'مدمن يبحث عن تعافي',
            'referral_source' => 'بحث جوجل',
            'volunteer_name' => 'متطوع تجريبي',
            'is_step_12' => false,
            'call_brief' => 'مكالمة بين الثلاثاء 3 والأول',
            'discuss_in_meeting' => false,
            'entry_time' => Carbon::now(),
        ]);

        $outsideCall = HelplineCall::create([
            'duration' => 'less_than_5',
            'call_date' => '2026-09-10', // inside 1st-to-3rd Tuesday window
            'call_time_shift' => '10:00 AM - 12:00 PM',
            'caller_type' => 'مدمن يبحث عن تعافي',
            'referral_source' => 'بحث جوجل',
            'volunteer_name' => 'متطوع تجريبي',
            'is_step_12' => false,
            'call_brief' => 'مكالمة خارج الفترة',
            'discuss_in_meeting' => false,
            'entry_time' => Carbon::now(),
        ]);

        $service = new HelplineReportService();
        $cycleKey = '2026-09-15_2026-10-05';

        // Available cycles should discover this meeting cycle because it has a call
        $cycles = $service->getAvailableCycles(12, true, 'meeting');
        $this->assertArrayHasKey($cycleKey, $cycles);
        $this->assertEquals('meeting', $cycles[$cycleKey]['group']);

        // Data endpoint with cycleKey returns the inside call and excludes the outside call
        $response = $this->actingAs($this->phoneUser)->getJson("/helpline/data?period_type=cycle&cycle={$cycleKey}");
        $response->assertStatus(200);

        $callIds = collect($response->json('calls.data'))->pluck('id');
        $this->assertTrue($callIds->contains($insideCall->id));
        $this->assertFalse($callIds->contains($outsideCall->id));
    }
}
