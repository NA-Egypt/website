<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceCommittee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkgroupHierarchyTest extends TestCase
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
        Role::firstOrCreate(['name' => 'Committees', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Workgroups', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);
    }

    public function test_service_committee_can_have_child_workgroups()
    {
        $parentComm = ServiceCommittee::create([
            'ar_name' => 'لجنة الخدمة الإقليمية',
            'en_name' => 'Regional Service Committee',
            'ar_address' => 'القاهرة',
            'en_address' => 'Cairo',
            'email' => 'rsc@naegypt.org',
        ]);

        $workgroup = ServiceCommittee::create([
            'parent_id' => $parentComm->id,
            'workgroup_type' => 'permanent',
            'status' => 'active',
            'ar_name' => 'مجموعة تقنية المعلومات',
            'en_name' => 'Information Technology Workgroup',
            'ar_address' => 'أونلاين',
            'en_address' => 'Online',
            'email' => 'web@naegypt.org',
            'notes' => 'Every 2nd Tuesday Online',
        ]);

        $this->assertTrue($parentComm->isCommittee());
        $this->assertFalse($parentComm->isWorkgroup());
        $this->assertTrue($workgroup->isWorkgroup());
        $this->assertFalse($workgroup->isCommittee());

        $this->assertEquals($parentComm->id, $workgroup->parent->id);
        $this->assertCount(1, $parentComm->workgroups);
        $this->assertEquals($workgroup->id, $parentComm->workgroups->first()->id);

        $this->assertEquals(1, ServiceCommittee::committeesOnly()->count());
        $this->assertEquals(1, ServiceCommittee::workgroupsOnly()->count());
    }

    public function test_committee_user_can_create_workgroup_under_their_committee()
    {
        $committeeUser = User::factory()->create(['email' => 'comm@naegypt.org']);
        $committeeUser->assignRole('Committees');

        $committee = ServiceCommittee::create([
            'ar_name' => 'لجنة الأنشطة',
            'en_name' => 'Activities Committee',
            'ar_address' => 'القاهرة',
            'en_address' => 'Cairo',
            'email' => 'comm@naegypt.org',
            'user_id' => $committeeUser->id,
        ]);

        $workgroupLead = User::factory()->create(['email' => 'wg@naegypt.org']);

        $this->actingAs($committeeUser);

        $response = $this->post(route('workgroup.store'), [
            'ar_name' => 'مجموعة عمل المؤتمر الثلاثون',
            'en_name' => 'Convention 30 Workgroup',
            'workgroup_type' => 'temporary',
            'status' => 'active',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(6)->format('Y-m-d'),
            'email' => (string)$workgroupLead->id,
            'notes' => 'Weekly Mondays 8 PM',
            'location' => 'https://zoom.us/j/123456',
            'ar_address' => 'أونلاين',
            'en_address' => 'Online',
        ]);

        $response->assertRedirect(route('workgroup.index'));

        $this->assertDatabaseHas('service_committees', [
            'ar_name' => 'مجموعة عمل المؤتمر الثلاثون',
            'parent_id' => $committee->id,
            'workgroup_type' => 'temporary',
            'status' => 'active',
            'user_id' => $workgroupLead->id,
        ]);

        $this->assertTrue($workgroupLead->fresh()->hasRole('Workgroups'));
    }

    public function test_parent_committee_can_view_and_update_its_workgroups_but_other_committees_cannot()
    {
        $parentUser = User::factory()->create(['email' => 'parent@naegypt.org']);
        $parentUser->assignRole('Committees');

        $otherUser = User::factory()->create(['email' => 'other@naegypt.org']);
        $otherUser->assignRole('Committees');

        $parentComm = ServiceCommittee::create([
            'ar_name' => 'اللجنة الرئيسية',
            'en_name' => 'Main Committee',
            'ar_address' => 'القاهرة',
            'en_address' => 'Cairo',
            'user_id' => $parentUser->id,
            'email' => 'parent@naegypt.org',
        ]);

        $otherComm = ServiceCommittee::create([
            'ar_name' => 'لجنة أخرى',
            'en_name' => 'Other Committee',
            'ar_address' => 'الجيزة',
            'en_address' => 'Giza',
            'user_id' => $otherUser->id,
            'email' => 'other@naegypt.org',
        ]);

        $wgUser = User::factory()->create(['email' => 'wglead@naegypt.org']);
        $wgUser->assignRole('Workgroups');

        $workgroup = ServiceCommittee::create([
            'parent_id' => $parentComm->id,
            'workgroup_type' => 'permanent',
            'status' => 'active',
            'ar_name' => 'مجموعة العمل التابعة',
            'en_name' => 'Sub Workgroup',
            'ar_address' => 'أونلاين',
            'en_address' => 'Online',
            'user_id' => $wgUser->id,
            'email' => 'wglead@naegypt.org',
        ]);

        // Parent committee user can view and edit
        $this->assertTrue($parentUser->can('view', $workgroup));
        $this->assertTrue($parentUser->can('update', $workgroup));

        // Workgroup user can view and edit own workgroup
        $this->assertTrue($wgUser->can('view', $workgroup));
        $this->assertTrue($wgUser->can('update', $workgroup));

        // Unrelated committee user CANNOT view or update
        $this->assertFalse($otherUser->can('view', $workgroup));
        $this->assertFalse($otherUser->can('update', $workgroup));
    }

    public function test_frontend_committees_page_loads_with_workgroups()
    {
        $parentComm = ServiceCommittee::create([
            'ar_name' => 'لجنة خدمة إقليم مصر',
            'en_name' => 'Egypt Regional Service Committee',
            'ar_address' => 'القاهرة',
            'en_address' => 'Cairo',
            'email' => 'rsc@naegypt.org',
        ]);

        ServiceCommittee::create([
            'parent_id' => $parentComm->id,
            'workgroup_type' => 'permanent',
            'status' => 'active',
            'ar_name' => 'مجموعة تقنية المعلومات',
            'en_name' => 'Information Technology Workgroup',
            'ar_address' => 'أونلاين',
            'en_address' => 'Online',
            'email' => 'web@naegypt.org',
            'notes' => 'Online Meetings',
            'location' => 'https://zoom.us/j/999',
        ]);

        $response = $this->get(route('frontend.comms'));

        $response->assertStatus(200);
        $response->assertSee('Egypt Regional Service Committee');
        $response->assertSee('Information Technology Workgroup');
        $response->assertSee('Online Meetings');
    }

    public function test_create_view_loads_successfully_for_super_admin_and_committee_user()
    {
        $admin = User::factory()->create(['email' => 'admin@naegypt.org']);
        $admin->assignRole('super admin');

        $response = $this->actingAs($admin)->get(route('workgroup.create'));
        $response->assertStatus(200);
        $response->assertSee(__('messages.Hierarchy & Classification'));

        $commUser = User::factory()->create(['email' => 'commlead@naegypt.org']);
        $commUser->assignRole('Committees');
        $committee = ServiceCommittee::create([
            'ar_name' => 'لجنة الأنشطة',
            'en_name' => 'Activities Committee',
            'ar_address' => 'القاهرة',
            'en_address' => 'Cairo',
            'email' => 'commlead@naegypt.org',
            'user_id' => $commUser->id,
        ]);

        $responseComm = $this->actingAs($commUser)->get(route('workgroup.create'));
        $responseComm->assertStatus(200);
        $responseComm->assertSee('لجنة الأنشطة');
    }

    public function test_workgroup_can_be_created_without_addresses()
    {
        $admin = User::factory()->create(['email' => 'admin2@naegypt.org']);
        $admin->assignRole('super admin');

        $parent = ServiceCommittee::create([
            'ar_name' => 'لجنة الخدمة الإقليمية',
            'en_name' => 'Regional Service Committee',
            'email' => 'rsc2@naegypt.org',
        ]);

        $lead = User::factory()->create(['email' => 'lead2@naegypt.org']);

        $response = $this->actingAs($admin)->post(route('workgroup.store'), [
            'parent_id' => $parent->id,
            'ar_name' => 'مجموعة العمل بدون عنوان',
            'en_name' => 'Workgroup Without Address',
            'workgroup_type' => 'permanent',
            'status' => 'active',
            'email' => (string)$lead->id,
            'location' => 'https://zoom.us/j/111222',
            // ar_address and en_address omitted
        ]);

        $response->assertRedirect(route('workgroup.index'));
        $response->assertSessionHas('success', __('messages.Workgroup created successfully'));

        $this->assertDatabaseHas('service_committees', [
            'ar_name' => 'مجموعة العمل بدون عنوان',
            'ar_address' => null,
            'en_address' => null,
        ]);
    }

    public function test_workgroup_reports_lifecycle_and_embedding_into_committee_report()
    {
        // 1. Parent committee and user
        $commUser = User::factory()->create(['email' => 'parentcomm@naegypt.org']);
        $commUser->assignRole('Committees');
        $parentComm = ServiceCommittee::create([
            'ar_name' => 'لجنة الأنشطة الرئيسية',
            'en_name' => 'Main Activities Committee',
            'email' => 'parentcomm@naegypt.org',
            'user_id' => $commUser->id,
        ]);

        // 2. Child workgroup and user
        $wgUser = User::factory()->create(['email' => 'subwg@naegypt.org']);
        $wgUser->assignRole('Workgroups');
        $childWg = ServiceCommittee::create([
            'parent_id' => $parentComm->id,
            'workgroup_type' => 'permanent',
            'status' => 'active',
            'ar_name' => 'مجموعة عمل الرياضة',
            'en_name' => 'Sports Workgroup',
            'email' => 'subwg@naegypt.org',
            'user_id' => $wgUser->id,
        ]);

        // 3. Workgroup user creates a report (even if submitted as 'submitted', must be forced to 'draft')
        $this->actingAs($wgUser);
        $wgResponse = $this->post(route('committee-reports.store'), [
            'meeting_date' => now()->format('Y-m-d'),
            'meeting_day_description' => 'First WG Meeting',
            'sections' => [
                ['headline' => 'Sports Event Prep', 'content' => '<p>Planning soccer tournament</p>']
            ],
            'status' => 'submitted', // Attempt to submit
        ]);

        $wgResponse->assertRedirect(route('committee-reports.index'));
        $wgReport = \App\Models\CommitteeReport::where('service_committee_id', $childWg->id)->first();
        $this->assertNotNull($wgReport);
        // Assert forced to draft
        $this->assertEquals('draft', $wgReport->status);

        // 4. Workgroup user attempts to send to RSC -> must be blocked
        $sendResponse = $this->post(route('committee-reports.send', $wgReport->id));
        $sendResponse->assertSessionHas('error');
        $this->assertEquals('draft', $wgReport->fresh()->status);

        // 5. Parent committee views reports index -> can see workgroup drafts tab and badge
        $this->actingAs($commUser);
        $indexResponse = $this->get(route('committee-reports.index', ['tab' => 'workgroups']));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('First WG Meeting');
        $indexResponse->assertSee('مجموعة عمل الرياضة');

        // 6. Parent committee creates their report and embeds the workgroup report
        $commResponse = $this->post(route('committee-reports.store'), [
            'meeting_date' => now()->format('Y-m-d'),
            'meeting_day_description' => 'Parent Monthly Meeting',
            'sections' => [
                ['headline' => 'General Overview', 'content' => '<p>All ongoing activities</p>'],
                ['headline' => '[Sports Workgroup] Sports Event Prep', 'content' => '<p>Planning soccer tournament - approved by committee</p>']
            ],
            'status' => 'submitted',
            'embedded_workgroup_report_ids' => [$wgReport->id],
        ]);

        $commResponse->assertRedirect(route('committee-reports.index'));

        $parentReport = \App\Models\CommitteeReport::where('service_committee_id', $parentComm->id)->first();
        $this->assertNotNull($parentReport);
        $this->assertEquals('submitted', $parentReport->status);

        // Assert workgroup report is linked and marked as embedded
        $freshWgReport = $wgReport->fresh();
        $this->assertEquals($parentReport->id, $freshWgReport->parent_report_id);
        $this->assertEquals('embedded', $freshWgReport->status);
        $this->assertTrue($freshWgReport->isEmbedded());
        $this->assertCount(1, $parentReport->embeddedWorkgroupReports);

        // 7. RSC user checks committee reports index
        $rscUser = User::factory()->create(['email' => 'rsclead@naegypt.org']);
        $rscUser->assignRole('rsc');
        $this->actingAs($rscUser);

        $rscResponse = $this->get(route('committee-reports.index'));
        $rscResponse->assertStatus(200);
        // Sees parent committee report
        $rscResponse->assertSee('لجنة الأنشطة الرئيسية');
        // Does NOT see standalone workgroup report in the table
        $rscResponse->assertDontSee('مجموعة عمل الرياضة');
    }

    public function test_rsc_role_sees_and_manages_only_rsc_workgroups()
    {
        // 1. Setup Egypt RSC committee (id: 83)
        $rscCommittee = new ServiceCommittee([
            'ar_name' => 'لجنة خدمة إقليم مصر',
            'en_name' => 'Egypt Regional Service Committee',
            'email' => 'RSC@naegypt.org',
            'ar_address' => 'القاهرة',
            'en_address' => 'Cairo',
        ]);
        $rscCommittee->id = 83;
        $rscCommittee->save();

        // 2. Setup IT Workgroup belonging to RSC
        $itWorkgroup = ServiceCommittee::create([
            'parent_id' => 83,
            'workgroup_type' => 'permanent',
            'status' => 'active',
            'ar_name' => 'مجموعة عمل تقنية المعلومات',
            'en_name' => 'IT Workgroup',
            'email' => 'web@naegypt.org',
        ]);

        // 3. Setup another committee and workgroup
        $otherComm = ServiceCommittee::create([
            'ar_name' => 'لجنة الترجمة',
            'en_name' => 'Translation Committee',
            'email' => 'trans@naegypt.org',
        ]);

        $transWorkgroup = ServiceCommittee::create([
            'parent_id' => $otherComm->id,
            'workgroup_type' => 'permanent',
            'status' => 'active',
            'ar_name' => 'مجموعة مراجعة النصوص',
            'en_name' => 'Text Review Workgroup',
            'email' => 'review@naegypt.org',
        ]);

        // 4. Create an RSC officer without user_id explicitly attached to committee 83
        $rcpUser = User::factory()->create(['email' => 'RCP@naegypt.org']);
        $rcpUser->assignRole('rsc');
        $this->actingAs($rcpUser);

        // 5. Test GET /workgroup view displays RSC committee header
        $viewResponse = $this->get(route('workgroup.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('لجنة خدمة إقليم مصر');

        // 6. Test AJAX datatable returns only IT Workgroup, not Translation Workgroup
        $jsonResponse = $this->getJson(route('workgroup.index'));
        $jsonResponse->assertStatus(200);
        $data = $jsonResponse->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($itWorkgroup->id, $data[0]['id']);
        $this->assertEquals('مجموعة عمل تقنية المعلومات', $data[0]['ar_name']);

        // 7. Test RSC user can view IT workgroup details
        $this->assertTrue($rcpUser->can('view', $itWorkgroup));
        $showResponse = $this->get(route('workgroup.show', $itWorkgroup->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('مجموعة عمل تقنية المعلومات');

        // 8. Test RSC user can edit IT workgroup
        $this->assertTrue($rcpUser->can('update', $itWorkgroup));
        $editResponse = $this->get(route('workgroup.edit', $itWorkgroup->id));
        $editResponse->assertStatus(200);

        // 9. Test RSC user CANNOT view or edit Translation Workgroup
        $this->assertFalse($rcpUser->can('view', $transWorkgroup));
        $this->assertFalse($rcpUser->can('update', $transWorkgroup));
        $this->assertFalse($rcpUser->can('delete', $transWorkgroup));

        // 10. Test RSC user can create a new workgroup under Committee 83
        $newWgLead = User::factory()->create(['email' => 'helpline_wg@naegypt.org']);
        $storeResponse = $this->post(route('workgroup.store'), [
            'ar_name' => 'مجموعة عمل خط المساعدة',
            'en_name' => 'Helpline Workgroup',
            'workgroup_type' => 'permanent',
            'status' => 'active',
            'email' => (string)$newWgLead->id,
        ]);
        $storeResponse->assertRedirect(route('workgroup.index'));

        $this->assertDatabaseHas('service_committees', [
            'ar_name' => 'مجموعة عمل خط المساعدة',
            'parent_id' => 83,
        ]);
    }

    public function test_rsc_sidebar_shows_my_committee_and_workgroups()
    {
        $rscCommittee = new ServiceCommittee([
            'ar_name' => 'لجنة خدمة إقليم مصر',
            'en_name' => 'Egypt Regional Service Committee',
            'email' => 'RSC@naegypt.org',
        ]);
        $rscCommittee->id = 83;
        $rscCommittee->save();

        $rscOfficer = User::factory()->create(['email' => 'ARSC@naegypt.org']);
        $rscOfficer->assignRole('rsc');
        $this->actingAs($rscOfficer);

        $view = $this->blade('<x-side-bar />');
        $view->assertSee(route('serviceCommittee.show', 83));
        $view->assertSee(route('workgroup.index'));
        $view->assertSee(__('messages.My Committee Details') ?? 'My Committee Details');
        $view->assertSee(__('messages.My Workgroups'));
    }
}
