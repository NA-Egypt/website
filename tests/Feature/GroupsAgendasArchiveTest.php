<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\Agenda;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GroupsAgendasArchiveTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }

    public function test_unauthorized_user_cannot_access_groups_agendas_archive()
    {
        // Unauthenticated
        $this->get(route('groups-agendas.archive'))->assertRedirect();

        // Authenticated but normal user (GSR)
        $user = User::factory()->create();
        $user->assignRole('gsr');
        $this->actingAs($user);

        $this->get(route('groups-agendas.archive'))->assertStatus(403);
    }

    public function test_super_admin_can_access_groups_agendas_archive_and_view_details()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super admin');
        $this->actingAs($superAdmin);

        $serviceBody = \App\Models\ServiceBody::first() ?? \App\Models\ServiceBody::create([
            'ar_name' => 'كيان خدمي',
            'en_name' => 'Service Body',
            'description' => 'Desc',
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
        $this->actingAs($rscUser);

        $response = $this->get(route('groups-agendas.archive'));
        $response->assertStatus(200);
    }
}
