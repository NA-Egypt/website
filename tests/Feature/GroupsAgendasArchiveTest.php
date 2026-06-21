<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\Agenda;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupsAgendasArchiveTest extends TestCase
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
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'ServiceBody', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'gsr', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);
    }

    public function test_unauthenticated_user_cannot_access_groups_agendas_archive()
    {
        // Unauthenticated
        $this->get(route('groups-agendas.archive'))->assertRedirect();
    }

    public function test_gsr_user_can_access_archive_and_see_only_own_group_agendas()
    {
        $day = \App\Models\Day::first() ?? \App\Models\Day::create([
            'ar_name' => 'السبت',
            'en_name' => 'Saturday',
        ]);
        $serviceBody = \App\Models\ServiceBody::create([
            'ar_name' => 'كيان خدمي',
            'en_name' => 'Service Body',
            'description' => 'Desc',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Location',
        ]);
        $city = \App\Models\City::first() ?? \App\Models\City::create([
            'ar_name' => 'القاهرة',
            'en_name' => 'Cairo',
        ]);
        $neighborhood = \App\Models\Neighborhood::first() ?? \App\Models\Neighborhood::create([
            'ar_name' => 'حي الاختبار',
            'en_name' => 'Test Neighborhood',
            'city_id' => $city->id,
        ]);

        $user1 = User::factory()->create();
        $user1->assignRole('gsr');

        $user2 = User::factory()->create();
        $user2->assignRole('gsr');

        $group1 = Group::create([
            'ar_name' => 'Group 1',
            'en_name' => 'Group 1',
            'ar_gsr_name' => 'GSR 1',
            'en_gsr_name' => 'GSR 1',
            'email' => 'g1@example.com',
            'phone' => '12345678',
            'user_id' => $user1->id,
            'ar_address' => 'عنوان 1',
            'en_address' => 'Address 1',
            'location' => 'https://maps.google.com/1',
            'group_type' => 'open',
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
        ]);

        $group2 = Group::create([
            'ar_name' => 'Group 2',
            'en_name' => 'Group 2',
            'ar_gsr_name' => 'GSR 2',
            'en_gsr_name' => 'GSR 2',
            'email' => 'g2@example.com',
            'phone' => '12345678',
            'user_id' => $user2->id,
            'ar_address' => 'عنوان 2',
            'en_address' => 'Address 2',
            'location' => 'https://maps.google.com/2',
            'group_type' => 'open',
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
        ]);

        $agenda1 = Agenda::create([
            'group_id' => $group1->id,
            'meetings_per_week' => 2,
            'agenda_date' => '2026-06-01',
            'service_position' => 'GSR',
            'submitter_name' => 'John Group1',
        ]);

        $agenda2 = Agenda::create([
            'group_id' => $group2->id,
            'meetings_per_week' => 3,
            'agenda_date' => '2026-06-02',
            'service_position' => 'GSR',
            'submitter_name' => 'Jane Group2',
        ]);

        // Login as User 1 (GSR of Group 1)
        $this->actingAs($user1);
        $response = $this->get(route('groups-agendas.archive'));
        $response->assertStatus(200);

        // Should see Group 1's agenda but not Group 2's
        $response->assertSee('John Group1');
        $response->assertDontSee('Jane Group2');
    }

    public function test_super_admin_can_access_groups_agendas_archive_and_view_details()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super admin');
        $this->actingAs($superAdmin);

        $day = \App\Models\Day::first() ?? \App\Models\Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $serviceBody = \App\Models\ServiceBody::first() ?? \App\Models\ServiceBody::create([
            'ar_name' => 'كيان خدمي',
            'en_name' => 'Service Body',
            'description' => 'Desc',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Test Location',
        ]);

        $city = \App\Models\City::first() ?? \App\Models\City::create([
            'ar_name' => 'القاهرة',
            'en_name' => 'Cairo',
        ]);

        $neighborhood = \App\Models\Neighborhood::first() ?? \App\Models\Neighborhood::create([
            'ar_name' => 'حي الاختبار',
            'en_name' => 'Test Neighborhood',
            'city_id' => $city->id,
        ]);

        $group = Group::create([
            'ar_name' => 'مجموعة الاختبار',
            'en_name' => 'Test Group',
            'ar_gsr_name' => 'ممثل المجموعة',
            'en_gsr_name' => 'GSR Name',
            'email' => 'test-group@naegypt.org',
            'phone' => '12345678',
            'user_id' => $superAdmin->id,
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'location' => 'https://maps.google.com',
            'group_type' => 'open',
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
        ]);

        $agenda = Agenda::create([
            'group_id' => $group->id,
            'meetings_per_week' => 2,
            'agenda_date' => '2026-06-01',
            'service_position' => 'GSR',
            'submitter_name' => 'John Doe',
            'new_comers' => 5,
        ]);

        $response = $this->get(route('groups-agendas.archive'));
        $response->assertStatus(200);
        $response->assertSee('مجموعة الاختبار');
        $response->assertSee('John Doe');

        // Can view specific agenda details
        $this->get(route('agenda.show', $agenda->id))->assertStatus(200);

        // Can download PDF
        $this->get(route('agenda.exportPdf', $agenda->id))->assertStatus(200);
    }

    public function test_rsc_email_user_can_access_groups_agendas_archive()
    {
        $rscUser = User::where('email', 'rsc@naegypt.org')->first() ?? User::factory()->create(['email' => 'rsc@naegypt.org']);
        $rscUser->assignRole('rsc');
        $this->actingAs($rscUser);

        $response = $this->get(route('groups-agendas.archive'));
        $response->assertStatus(200);
    }

    public function test_servicebody_user_can_only_access_their_own_groups_agendas_in_archive()
    {
        $day = \App\Models\Day::first() ?? \App\Models\Day::create([
            'ar_name' => 'السبت',
            'en_name' => 'Saturday',
        ]);

        // Setup two different service bodies
        $sb1 = \App\Models\ServiceBody::create([
            'ar_name' => 'كيان خدمي 1',
            'en_name' => 'Service Body 1',
            'description' => 'Desc 1',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Location 1',
        ]);
        $sb2 = \App\Models\ServiceBody::create([
            'ar_name' => 'كيان خدمي 2',
            'en_name' => 'Service Body 2',
            'description' => 'Desc 2',
            'day_id' => $day->id,
            'date' => '2026-06-02',
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'location' => 'Location 2',
        ]);

        $city = \App\Models\City::first() ?? \App\Models\City::create([
            'ar_name' => 'القاهرة',
            'en_name' => 'Cairo',
        ]);
        $neighborhood = \App\Models\Neighborhood::first() ?? \App\Models\Neighborhood::create([
            'ar_name' => 'حي الاختبار',
            'en_name' => 'Test Neighborhood',
            'city_id' => $city->id,
        ]);

        // Create groups
        $group1 = Group::create([
            'ar_name' => 'Group 1',
            'en_name' => 'Group 1',
            'ar_gsr_name' => 'ممثل 1',
            'en_gsr_name' => 'GSR 1',
            'email' => 'g1@example.com',
            'phone' => '12345678',
            'user_id' => User::factory()->create()->id,
            'ar_address' => 'عنوان 1',
            'en_address' => 'Address 1',
            'location' => 'https://maps.google.com/1',
            'group_type' => 'open',
            'service_body_id' => $sb1->id,
            'neighborhood_id' => $neighborhood->id,
        ]);
        $group2 = Group::create([
            'ar_name' => 'Group 2',
            'en_name' => 'Group 2',
            'ar_gsr_name' => 'ممثل 2',
            'en_gsr_name' => 'GSR 2',
            'email' => 'g2@example.com',
            'phone' => '12345678',
            'user_id' => User::factory()->create()->id,
            'ar_address' => 'عنوان 2',
            'en_address' => 'Address 2',
            'location' => 'https://maps.google.com/2',
            'group_type' => 'open',
            'service_body_id' => $sb2->id,
            'neighborhood_id' => $neighborhood->id,
        ]);

        // Create agendas
        $agenda1 = Agenda::create([
            'group_id' => $group1->id,
            'meetings_per_week' => 2,
            'agenda_date' => '2026-06-01',
            'service_position' => 'GSR',
            'submitter_name' => 'John SB1',
        ]);
        $agenda2 = Agenda::create([
            'group_id' => $group2->id,
            'meetings_per_week' => 3,
            'agenda_date' => '2026-06-02',
            'service_position' => 'GSR',
            'submitter_name' => 'Jane SB2',
        ]);

        // Authenticate as a user belonging to ServiceBody 1
        $sbUser = User::factory()->create([
            'service_body_id' => $sb1->id,
        ]);
        $sbUser->assignRole('ServiceBody');
        $this->actingAs($sbUser);

        // Access archive
        $response = $this->get(route('groups-agendas.archive'));
        $response->assertStatus(200);

        // Should see Group 1's agenda but not Group 2's
        $response->assertSee('John SB1');
        $response->assertDontSee('Jane SB2');

        // Can view specific agenda details of Group 1
        $this->get(route('agenda.show', $agenda1->id))->assertStatus(200);

        // Cannot view specific agenda details of Group 2
        $this->get(route('agenda.show', $agenda2->id))->assertStatus(403);
    }

    public function test_agenda_store_requires_trusted_servants()
    {
        $user = User::factory()->create();
        $user->assignRole('super admin');
        $this->actingAs($user);

        $day = \App\Models\Day::first() ?? \App\Models\Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $serviceBody = \App\Models\ServiceBody::create([
            'ar_name' => 'كيان خدمي',
            'en_name' => 'Service Body',
            'description' => 'Desc',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Test Location',
        ]);
        $city = \App\Models\City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = \App\Models\Neighborhood::create([
            'ar_name' => 'حي الاختبار',
            'en_name' => 'Test Neighborhood',
            'city_id' => $city->id,
        ]);
        $group = Group::create([
            'ar_name' => 'مجموعة الاختبار',
            'en_name' => 'Test Group',
            'ar_gsr_name' => 'ممثل المجموعة',
            'en_gsr_name' => 'GSR Name',
            'email' => 'test-group@naegypt.org',
            'phone' => '12345678',
            'user_id' => $user->id,
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'location' => 'https://maps.google.com',
            'group_type' => 'open',
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
        ]);

        $baseData = [
            'group_id' => $group->id,
            'meetings_per_week' => 2,
            'agenda_date' => '2026-06-01',
            'service_position' => 'GSR',
            'submitter_name' => 'John Doe',
            'new_comers' => 5,
            'recovery_atmosphere' => 'Good recovery',
            'financial_issues' => 'All good',
        ];

        // Should fail when trusted_servants is missing
        $response = $this->post(route('agenda.store'), $baseData);
        $response->assertSessionHasErrors(['trusted_servants']);

        // Should succeed when trusted_servants is provided
        $validData = array_merge($baseData, [
            'trusted_servants' => 'Active trusted servants here',
        ]);
        $response = $this->post(route('agenda.store'), $validData);
        $response->assertRedirect(route('group.show', $group->id));

        $this->assertDatabaseHas('agendas', [
            'group_id' => $group->id,
            'trusted_servants' => 'Active trusted servants here',
        ]);
    }

    public function test_groups_agendas_archive_dynamic_header_for_selected_group()
    {
        $user = User::factory()->create();
        $user->assignRole('gsr');
        $this->actingAs($user);

        $day = \App\Models\Day::first() ?? \App\Models\Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $serviceBody = \App\Models\ServiceBody::first() ?? \App\Models\ServiceBody::create([
            'ar_name' => 'كيان خدمي',
            'en_name' => 'Service Body',
            'description' => 'Desc',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Test Location',
        ]);
        $city = \App\Models\City::first() ?? \App\Models\City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = \App\Models\Neighborhood::first() ?? \App\Models\Neighborhood::create([
            'ar_name' => 'حي الاختبار',
            'en_name' => 'Test Neighborhood',
            'city_id' => $city->id,
        ]);
        $group = Group::create([
            'ar_name' => 'مجموعة الاختبار المميزة',
            'en_name' => 'Special Test Group',
            'ar_gsr_name' => 'GSR',
            'en_gsr_name' => 'GSR',
            'email' => 'tg@example.com',
            'phone' => '12345678',
            'user_id' => $user->id,
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'location' => 'https://maps.google.com',
            'group_type' => 'open',
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
        ]);

        // When group_id parameter is passed, we should see the group's archive title in English
        app()->setLocale('en');
        $response = $this->get(route('groups-agendas.archive', ['group_id' => $group->id]));
        $response->assertStatus(200);
        $response->assertSee('Agendas Archive of Special Test Group');

        // And in Arabic
        app()->setLocale('ar');
        $response = $this->get(route('groups-agendas.archive', ['group_id' => $group->id]));
        $response->assertStatus(200);
        $response->assertSee('أرشيف أجندات مجموعة الاختبار المميزة');
    }

    public function test_super_admin_and_rsc_can_see_all_groups_agendas_in_archive()
    {
        $day = \App\Models\Day::first() ?? \App\Models\Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $serviceBody = \App\Models\ServiceBody::first() ?? \App\Models\ServiceBody::create([
            'ar_name' => 'كيان خدمي',
            'en_name' => 'Service Body',
            'description' => 'Desc',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Test Location',
        ]);
        $city = \App\Models\City::first() ?? \App\Models\City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = \App\Models\Neighborhood::first() ?? \App\Models\Neighborhood::create([
            'ar_name' => 'حي الاختبار',
            'en_name' => 'Test Neighborhood',
            'city_id' => $city->id,
        ]);

        $user1 = User::factory()->create();
        $group1 = Group::create([
            'ar_name' => 'Group One',
            'en_name' => 'Group One',
            'ar_gsr_name' => 'GSR1',
            'en_gsr_name' => 'GSR1',
            'email' => 'g1@example.com',
            'phone' => '12345678',
            'user_id' => $user1->id,
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'location' => 'https://maps.google.com',
            'group_type' => 'open',
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
        ]);

        $user2 = User::factory()->create();
        $group2 = Group::create([
            'ar_name' => 'Group Two',
            'en_name' => 'Group Two',
            'ar_gsr_name' => 'GSR2',
            'en_gsr_name' => 'GSR2',
            'email' => 'g2@example.com',
            'phone' => '87654321',
            'user_id' => $user2->id,
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'location' => 'https://maps.google.com',
            'group_type' => 'open',
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
        ]);

        $agenda1 = Agenda::create([
            'group_id' => $group1->id,
            'meetings_per_week' => 2,
            'agenda_date' => '2026-06-01',
            'service_position' => 'GSR',
            'submitter_name' => 'Submitter One',
        ]);

        $agenda2 = Agenda::create([
            'group_id' => $group2->id,
            'meetings_per_week' => 3,
            'agenda_date' => '2026-06-02',
            'service_position' => 'GSR',
            'submitter_name' => 'Submitter Two',
        ]);

        // As Super Admin
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super admin');
        $this->actingAs($superAdmin);

        $response = $this->get(route('groups-agendas.archive'));
        $response->assertStatus(200);
        $response->assertSee('Submitter One');
        $response->assertSee('Submitter Two');

        // As RSC member
        $rscUser = User::factory()->create();
        $rscUser->assignRole('rsc');
        $this->actingAs($rscUser);

        $response = $this->get(route('groups-agendas.archive'));
        $response->assertStatus(200);
        $response->assertSee('Submitter One');
        $response->assertSee('Submitter Two');
    }
}
