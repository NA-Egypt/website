<?php

namespace Tests\Feature\Api;

use App\Models\City;
use App\Models\CommitteeReport;
use App\Models\ContactUs;
use App\Models\Day;
use App\Models\Group;
use App\Models\Neighborhood;
use App\Models\NewsletterMember;
use App\Models\Role;
use App\Models\ServiceBody;
use App\Models\ServiceCommittee;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
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

        $this->user = User::factory()->create();

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
            'status' => 'approved',
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
}
