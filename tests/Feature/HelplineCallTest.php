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
    }

    public function test_public_api_submission_endpoint()
    {
        $payload = [
            'duration' => 'more_than_5',
            'call_date' => Carbon::today()->format('Y-m-d'),
            'call_time_shift' => '4:00 PM - 6:00 PM',
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
        $response->assertJsonPath('data.is_step_12', true);
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
        // e.g. Sep 16, 2026: Cycle should start at end of Sep 1, and end at 1st Tuesday of Oct 2026
        $now = Carbon::create(2026, 9, 16, 12, 0, 0);
        $cycle = $service->getCurrentCycleWindow($now);

        $this->assertEquals('2026-09-01 23:59:59', $cycle['start']->format('Y-m-d H:i:s'));
        $this->assertEquals(Carbon::TUESDAY, $cycle['end']->dayOfWeek);
        $this->assertEquals(10, $cycle['end']->month);
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

    public function test_empty_past_cycles_are_filtered_and_only_current_cycle_kept()
    {
        $service = new HelplineReportService();

        // With no calls in past cycles, only 1 cycle (the current cycle) should be returned
        $cycles = $service->getAvailableCycles(12, true);
        $this->assertCount(1, $cycles);
        $currentCycle = array_values($cycles)[0];
        $this->assertTrue($currentCycle['is_current']);
    }
}
