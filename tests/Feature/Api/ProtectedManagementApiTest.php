<?php

namespace Tests\Feature\Api;

use App\Models\Agenda;
use App\Models\CalendarEvent;
use App\Models\City;
use App\Models\CommitteeReport;
use App\Models\ContactUs;
use App\Models\CustomForm;
use App\Models\Day;
use App\Models\DirectOnlineGroup;
use App\Models\Event;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\NewsletterMember;
use App\Models\Option;
use App\Models\ScMeeting;
use App\Models\ServiceBody;
use App\Models\ServiceBodyAgenda;
use App\Models\ServiceCommittee;
use App\Models\Topic;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;
use Tests\TestCase;

class ProtectedManagementApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Day $saturday;

    private City $cairo;

    private Neighborhood $downtown;

    private ServiceBody $cairoArea;

    private Group $hopeGroup;

    private ServiceCommittee $serviceCommittee;

    protected function setUp(): void
    {
        parent::setUp();

        $superAdminRole = SpatieRole::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $this->user = User::factory()->create();
        $this->user->assignRole($superAdminRole);

        $this->saturday = Day::create([
            'ar_name' => 'السبت',
            'en_name' => 'Saturday',
        ]);

        $this->cairo = City::create([
            'ar_name' => 'القاهرة',
            'en_name' => 'Cairo',
            'user_id' => $this->user->id,
        ]);

        $this->downtown = Neighborhood::create([
            'city_id' => $this->cairo->id,
            'ar_name' => 'وسط البلد',
            'en_name' => 'Downtown',
            'user_id' => $this->user->id,
        ]);

        $this->cairoArea = ServiceBody::create([
            'ar_name' => 'لجنة القاهرة',
            'en_name' => 'Cairo Area',
            'day_id' => $this->saturday->id,
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
            'location' => 'Cairo',
        ]);

        $this->hopeGroup = Group::create([
            'ar_name' => 'مجموعة الأمل',
            'en_name' => 'Hope Group',
            'ar_gsr_name' => 'أحمد',
            'en_gsr_name' => 'Ahmed',
            'phone' => '+201000000000',
            'location' => 'https://maps.google.com/?q=30.0444,31.2357',
            'ar_address' => 'وسط البلد، القاهرة',
            'en_address' => 'Downtown, Cairo',
            'service_body_id' => $this->cairoArea->id,
            'neighborhood_id' => $this->downtown->id,
            'user_id' => $this->user->id,
            'group_type' => 'in_person',
        ]);

        $this->serviceCommittee = ServiceCommittee::create([
            'ar_name' => 'لجنة المستشفيات والمؤسسات',
            'en_name' => 'H&I Committee',
            'ar_address' => 'وسط البلد، القاهرة',
            'en_address' => 'Downtown, Cairo',
            'location' => 'Cairo',
        ]);
    }

    public function test_unauthenticated_requests_are_rejected_with_401(): void
    {
        $this->getJson('/api/v1/committee-reports')->assertStatus(401);
        $this->getJson('/api/v1/contact-requests')->assertStatus(401);
        $this->getJson('/api/v1/contact-us')->assertStatus(401);
        $this->getJson('/api/v1/newsletter-members')->assertStatus(401);
        $this->getJson('/api/v1/transactions')->assertStatus(401);
        $this->getJson('/api/v1/users')->assertStatus(401);
        $this->getJson('/api/v1/roles')->assertStatus(401);
        $this->getJson('/api/v1/permissions')->assertStatus(401);
    }

    public function test_committee_reports_crud(): void
    {
        Sanctum::actingAs($this->user);

        $reportRes = $this->postJson('/api/v1/committee-reports', [
            'service_committee_id' => $this->serviceCommittee->id,
            'meeting_date' => '2026-08-01',
            'report_date' => '2026-08-01',
            'body' => 'Monthly H&I Report summary',
            'status' => 'draft',
            'user_id' => $this->user->id,
        ]);
        $reportRes->assertStatus(201);
        $reportId = $reportRes->json('data.id');

        $this->getJson('/api/v1/committee-reports')->assertStatus(200);
        $this->getJson("/api/v1/committee-reports/{$reportId}")->assertStatus(200);

        $this->deleteJson("/api/v1/committee-reports/{$reportId}")->assertStatus(204);
    }

    public function test_contact_us_and_contact_requests_crud(): void
    {
        Sanctum::actingAs($this->user);

        $res = $this->postJson('/api/v1/contact-us', [
            'name' => 'Visitor Name',
            'email' => 'visitor@example.com',
            'phone' => '+201001234567',
            'subject' => 'Meeting Inquiry',
            'message' => 'Is the meeting in Maadi open to newcomers?',
        ]);
        $res->assertStatus(201);
        $contactId = $res->json('data.id');

        $this->getJson('/api/v1/contact-requests')->assertStatus(200);
        $this->getJson("/api/v1/contact-us/{$contactId}")->assertStatus(200);

        $this->deleteJson("/api/v1/contact-us/{$contactId}")->assertStatus(204);
    }

    public function test_newsletter_members_crud(): void
    {
        Sanctum::actingAs($this->user);

        $res = $this->postJson('/api/v1/newsletter-members', [
            'email' => 'subscriber@naegypt.org',
            'name' => 'Newsletter Subscriber',
        ]);
        $res->assertStatus(201);
        $memberId = $res->json('data.id');

        $this->getJson('/api/v1/newsletter-members')->assertStatus(200);
        $this->deleteJson("/api/v1/newsletter-members/{$memberId}")->assertStatus(204);
    }

    public function test_financial_transactions_crud(): void
    {
        Sanctum::actingAs($this->user);

        $res = $this->postJson('/api/v1/transactions', [
            'model' => 'Group',
            'operation' => 'create',
            'details' => ['name' => 'New Group', 'city' => 'Cairo'],
        ]);
        $res->assertStatus(201);
        $transactionId = $res->json('data.id');

        $this->getJson('/api/v1/transactions')->assertStatus(200);
        $this->getJson("/api/v1/transactions/{$transactionId}")->assertStatus(200);

        $this->deleteJson("/api/v1/transactions/{$transactionId}")->assertStatus(204);
    }

    public function test_users_roles_and_permissions_crud(): void
    {
        Sanctum::actingAs($this->user);

        // User management
        $userRes = $this->postJson('/api/v1/users', [
            'name' => 'New Servant',
            'email' => 'servant@naegypt.org',
            'password' => 'secret12345',
            'service_body_id' => $this->cairoArea->id,
        ]);
        $userRes->assertStatus(201);
        $newUserId = $userRes->json('data.id');

        $this->getJson('/api/v1/users')->assertStatus(200);
        $this->getJson("/api/v1/users/{$newUserId}")->assertStatus(200);
        $this->deleteJson("/api/v1/users/{$newUserId}")->assertStatus(204);

        // Roles & Permissions lookups
        $this->getJson('/api/v1/roles')->assertStatus(200);
        $this->getJson('/api/v1/permissions')->assertStatus(200);
    }

    public function test_authenticated_user_without_permissions_receives_403_on_management_reads_and_writes(): void
    {
        $plainUser = User::factory()->create();
        $targetUser = User::factory()->create();
        $role = SpatieRole::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'view something', 'guard_name' => 'web']);
        $transaction = Transaction::create([
            'model' => 'Group',
            'operation' => 'create',
            'details' => ['name' => 'Test'],
            'user_id' => $this->user->id,
        ]);
        $contact = ContactUs::create([
            'name' => 'Test',
            'email' => 'test@example.com',
            'message' => 'Hello',
        ]);
        $newsletter = NewsletterMember::create([
            'email' => 'news@example.com',
        ]);
        $report = CommitteeReport::create([
            'service_committee_id' => $this->serviceCommittee->id,
            'meeting_date' => '2026-08-01',
            'status' => 'submitted',
            'user_id' => $this->user->id,
        ]);
        $form = CustomForm::create([
            'title' => 'Form',
            'status' => 'published',
            'slug' => 'test-form-auth',
            'user_id' => $this->user->id,
        ]);

        Sanctum::actingAs($plainUser);

        // Management reads
        $this->getJson('/api/v1/users')->assertStatus(403);
        $this->getJson("/api/v1/users/{$targetUser->id}")->assertStatus(403);
        $this->getJson('/api/v1/roles')->assertStatus(403);
        $this->getJson("/api/v1/roles/{$role->id}")->assertStatus(403);
        $this->getJson('/api/v1/permissions')->assertStatus(403);
        $this->getJson("/api/v1/permissions/{$permission->id}")->assertStatus(403);
        $this->getJson('/api/v1/transactions')->assertStatus(403);
        $this->getJson("/api/v1/transactions/{$transaction->id}")->assertStatus(403);
        $this->getJson('/api/v1/committee-reports')->assertStatus(403);
        $this->getJson("/api/v1/committee-reports/{$report->id}")->assertStatus(403);
        $this->getJson('/api/v1/contact-us')->assertStatus(403);
        $this->getJson("/api/v1/contact-us/{$contact->id}")->assertStatus(403);
        $this->getJson('/api/v1/contact-requests')->assertStatus(403);
        $this->getJson('/api/v1/newsletter-members')->assertStatus(403);
        $this->getJson("/api/v1/newsletter-members/{$newsletter->id}")->assertStatus(403);
        $this->getJson('/api/v1/forms')->assertStatus(403);
        $this->getJson("/api/v1/forms/{$form->id}")->assertStatus(403);
        $this->getJson("/api/v1/forms/{$form->id}/submissions")->assertStatus(403);
        $this->getJson('/api/v1/helpline-calls')->assertStatus(403);

        // Management writes
        $this->postJson('/api/v1/users', ['name' => 'X', 'email' => 'x@na.org'])->assertStatus(403);
        $this->putJson("/api/v1/users/{$targetUser->id}", ['name' => 'Y'])->assertStatus(403);
        $this->deleteJson("/api/v1/users/{$targetUser->id}")->assertStatus(403);
        $this->postJson('/api/v1/roles', ['name' => 'hacker'])->assertStatus(403);
        $this->deleteJson("/api/v1/roles/{$role->id}")->assertStatus(403);
        $this->postJson('/api/v1/permissions', ['name' => 'hack'])->assertStatus(403);
        $this->deleteJson("/api/v1/permissions/{$permission->id}")->assertStatus(403);
        $this->postJson('/api/v1/transactions', ['model' => 'X', 'operation' => 'create', 'details' => []])->assertStatus(403);
        $this->deleteJson("/api/v1/transactions/{$transaction->id}")->assertStatus(403);
        $this->postJson('/api/v1/committee-reports', ['service_committee_id' => $this->serviceCommittee->id])->assertStatus(403);
        $this->putJson("/api/v1/committee-reports/{$report->id}", ['body' => 'Hacked'])->assertStatus(403);
        $this->deleteJson("/api/v1/committee-reports/{$report->id}")->assertStatus(403);
        $this->postJson('/api/v1/contact-us', ['name' => 'X', 'email' => 'x@x.com', 'message' => 'hi'])->assertStatus(403);
        $this->deleteJson("/api/v1/contact-us/{$contact->id}")->assertStatus(403);
        $this->postJson('/api/v1/newsletter-members', ['email' => 'x@x.com'])->assertStatus(403);
        $this->deleteJson("/api/v1/newsletter-members/{$newsletter->id}")->assertStatus(403);
        $this->postJson('/api/v1/forms', ['title' => 'X', 'status' => 'draft'])->assertStatus(403);
        $this->deleteJson("/api/v1/forms/{$form->id}")->assertStatus(403);
    }

    public function test_authenticated_user_without_permissions_receives_403_on_all_public_resource_writes(): void
    {
        $plainUser = User::factory()->create();
        $topic = Topic::create(['ar_name' => 'موضوع', 'en_name' => 'Topic']);
        $option = Option::create(['ar_name' => 'خيار', 'en_name' => 'Option']);
        $meeting = Meeting::create([
            'group_id' => $this->hopeGroup->id,
            'day_id' => $this->saturday->id,
            'topic_id' => $topic->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'type' => 'in_person',
            'lang' => 'arabic',
            'status' => 'available',
        ]);
        $directOnline = DirectOnlineGroup::create([
            'ar_name' => 'أونلاين',
            'en_name' => 'Online',
            'location' => 'https://zoom.us/j/111',
            'user_id' => $this->user->id,
        ]);
        $workgroup = ServiceCommittee::create([
            'ar_name' => 'مجموعة عمل',
            'en_name' => 'Workgroup',
            'parent_id' => $this->serviceCommittee->id,
            'workgroup_type' => 'standing',
            'status' => 'active',
        ]);
        $agenda = Agenda::create([
            'group_id' => $this->hopeGroup->id,
            'agenda_date' => '2026-08-01',
            'service_position' => 'GSR',
            'recovery_atmosphere' => 'Good',
            'trusted_servants' => 'Active',
            'financial_issues' => 'None',
        ]);
        $scMeeting = ScMeeting::create([
            'service_committee_id' => $this->serviceCommittee->id,
            'day_id' => $this->saturday->id,
            'week_number' => 1,
            'time' => '19:00',
        ]);
        $event = Event::create(['name' => 'Event', 'description' => 'Desc']);
        $calendarEvent = CalendarEvent::create([
            'title' => 'Cal',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-01',
            'user_id' => $this->user->id,
        ]);
        $sbAgenda = ServiceBodyAgenda::create([
            'service_body_id' => $this->cairoArea->id,
            'agenda_date' => '2026-08-01',
            'meeting_date' => '2026-08-15',
            'body' => [['headline' => 'H', 'content' => 'C']],
            'status' => 'draft',
        ]);

        Sanctum::actingAs($plainUser);

        $this->postJson('/api/v1/meetings', [])->assertStatus(403);
        $this->putJson("/api/v1/meetings/{$meeting->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/meetings/{$meeting->id}")->assertStatus(403);

        $this->postJson('/api/v1/groups', ['ar_name' => 'X'])->assertStatus(403);
        $this->putJson("/api/v1/groups/{$this->hopeGroup->id}", ['ar_name' => 'X'])->assertStatus(403);
        $this->deleteJson("/api/v1/groups/{$this->hopeGroup->id}")->assertStatus(403);

        $this->postJson('/api/v1/direct-online-groups', [])->assertStatus(403);
        $this->putJson("/api/v1/direct-online-groups/{$directOnline->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/direct-online-groups/{$directOnline->id}")->assertStatus(403);

        $this->postJson('/api/v1/service-committees', [])->assertStatus(403);
        $this->putJson("/api/v1/service-committees/{$this->serviceCommittee->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/service-committees/{$this->serviceCommittee->id}")->assertStatus(403);

        $this->postJson('/api/v1/workgroups', ['parent_id' => $this->serviceCommittee->id, 'workgroup_type' => 'standing', 'status' => 'active', 'ar_name' => 'X', 'en_name' => 'X'])->assertStatus(403);
        $this->putJson("/api/v1/workgroups/{$workgroup->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/workgroups/{$workgroup->id}")->assertStatus(403);

        $this->postJson('/api/v1/agendas', [])->assertStatus(403);
        $this->putJson("/api/v1/agendas/{$agenda->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/agendas/{$agenda->id}")->assertStatus(403);

        $this->postJson('/api/v1/service-bodies', [])->assertStatus(403);
        $this->putJson("/api/v1/service-bodies/{$this->cairoArea->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/service-bodies/{$this->cairoArea->id}")->assertStatus(403);

        $this->postJson('/api/v1/cities', [])->assertStatus(403);
        $this->putJson("/api/v1/cities/{$this->cairo->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/cities/{$this->cairo->id}")->assertStatus(403);

        $this->postJson('/api/v1/neighborhoods', [])->assertStatus(403);
        $this->putJson("/api/v1/neighborhoods/{$this->downtown->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/neighborhoods/{$this->downtown->id}")->assertStatus(403);

        $this->postJson('/api/v1/topics', [])->assertStatus(403);
        $this->putJson("/api/v1/topics/{$topic->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/topics/{$topic->id}")->assertStatus(403);

        $this->postJson('/api/v1/days', [])->assertStatus(403);
        $this->putJson("/api/v1/days/{$this->saturday->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/days/{$this->saturday->id}")->assertStatus(403);

        $this->postJson('/api/v1/options', [])->assertStatus(403);
        $this->putJson("/api/v1/options/{$option->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/options/{$option->id}")->assertStatus(403);

        $this->postJson('/api/v1/sc-meetings', [])->assertStatus(403);
        $this->putJson("/api/v1/sc-meetings/{$scMeeting->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/sc-meetings/{$scMeeting->id}")->assertStatus(403);

        $this->postJson('/api/v1/events', [])->assertStatus(403);
        $this->putJson("/api/v1/events/{$event->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/events/{$event->id}")->assertStatus(403);

        $this->postJson('/api/v1/calendar-events', [])->assertStatus(403);
        $this->putJson("/api/v1/calendar-events/{$calendarEvent->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/calendar-events/{$calendarEvent->id}")->assertStatus(403);

        $this->postJson('/api/v1/service-body-agendas', [])->assertStatus(403);
        $this->putJson("/api/v1/service-body-agendas/{$sbAgenda->id}", [])->assertStatus(403);
        $this->deleteJson("/api/v1/service-body-agendas/{$sbAgenda->id}")->assertStatus(403);
    }

    public function test_gsr_and_committee_scoping_is_enforced_on_api(): void
    {
        $gsrRole = SpatieRole::firstOrCreate(['name' => 'gsr', 'guard_name' => 'web']);
        $gsrUser = User::factory()->create();
        $gsrUser->assignRole($gsrRole);

        $otherUser = User::factory()->create();
        $otherGroup = Group::create([
            'ar_name' => 'مجموعة أخرى',
            'en_name' => 'Other Group',
            'ar_gsr_name' => 'أحمد',
            'en_gsr_name' => 'Ahmed',
            'phone' => '+201000000001',
            'location' => 'https://maps.google.com/?q=30.0444,31.2357',
            'ar_address' => 'وسط البلد، القاهرة',
            'en_address' => 'Downtown, Cairo',
            'service_body_id' => $this->cairoArea->id,
            'neighborhood_id' => $this->downtown->id,
            'user_id' => $otherUser->id,
            'group_type' => 'in_person',
        ]);

        $ownGroup = Group::create([
            'ar_name' => 'مجموعتي',
            'en_name' => 'My Group',
            'ar_gsr_name' => 'أحمد',
            'en_gsr_name' => 'Ahmed',
            'phone' => '+201000000002',
            'location' => 'https://maps.google.com/?q=30.0444,31.2357',
            'ar_address' => 'وسط البلد، القاهرة',
            'en_address' => 'Downtown, Cairo',
            'service_body_id' => $this->cairoArea->id,
            'neighborhood_id' => $this->downtown->id,
            'user_id' => $gsrUser->id,
            'group_type' => 'in_person',
        ]);

        Sanctum::actingAs($gsrUser);

        // GSR can update own group, but not another user's group
        $this->putJson("/api/v1/groups/{$ownGroup->id}", ['en_name' => 'Updated My Group'])->assertStatus(200);
        $this->putJson("/api/v1/groups/{$otherGroup->id}", ['en_name' => 'Hacked'])->assertStatus(403);

        // GSR cannot create meeting for another user's group
        $this->postJson('/api/v1/meetings', [
            'group_id' => $otherGroup->id,
            'day_id' => $this->saturday->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'type' => 'in_person',
            'lang' => 'arabic',
            'status' => 'available',
        ])->assertStatus(403);

        // Committee user scoping on committee reports
        $committeeUser = User::factory()->create();
        $ownCommittee = ServiceCommittee::create([
            'ar_name' => 'لجنة الإعلام',
            'en_name' => 'PI Committee',
            'user_id' => $committeeUser->id,
        ]);

        Sanctum::actingAs($committeeUser);

        $this->postJson('/api/v1/committee-reports', [
            'service_committee_id' => $ownCommittee->id,
            'meeting_date' => '2026-08-01',
            'body' => 'Own committee draft',
            'status' => 'draft',
        ])->assertStatus(201);

        $this->postJson('/api/v1/committee-reports', [
            'service_committee_id' => $this->serviceCommittee->id,
            'body' => 'Other committee draft',
            'status' => 'draft',
        ])->assertStatus(403);
    }
}
