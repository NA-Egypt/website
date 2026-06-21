<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\Day;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\Agenda;
use App\Models\ServiceBodyAgenda;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAgendasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'rsc']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'ServiceBody']);
    }

    protected function createServiceBody($attributes = [])
    {
        $day = Day::first() ?? Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        return ServiceBody::create(array_merge([
            'ar_name' => 'منطقة تجريبية',
            'en_name' => 'Test Service Body',
            'description' => 'Desc',
            'type' => 'rsc',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Cairo',
        ], $attributes));
    }

    protected function createGroup($serviceBody)
    {
        $user = User::factory()->create();
        $city = City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = Neighborhood::create([
            'ar_name' => 'حي الاختبار',
            'en_name' => 'Test Neighborhood',
            'city_id' => $city->id,
        ]);
        return Group::create([
            'ar_name' => 'مجموعة الاختبار',
            'en_name' => 'Test Group',
            'ar_gsr_name' => 'GSR',
            'en_gsr_name' => 'GSR',
            'email' => 'group@naegypt.org',
            'phone' => '12345678',
            'user_id' => $user->id,
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'location' => 'https://maps.google.com',
            'group_type' => 'open',
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
        ]);
    }

    public function test_public_can_list_and_view_group_agendas()
    {
        $sb = $this->createServiceBody();
        $group = $this->createGroup($sb);

        $agenda = Agenda::create([
            'group_id' => $group->id,
            'meetings_per_week' => 2,
            'agenda_date' => '2026-06-01',
            'service_position' => 'GSR',
            'submitter_name' => 'Test Submitter',
            'new_comers' => 5,
            'recovery_atmosphere' => 'Great atmosphere',
            'trusted_servants' => 'Active trusted servants',
            'financial_issues' => 'None',
        ]);

        $response = $this->getJson('/api/agendas');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'submitter_name' => 'Test Submitter',
        ]);

        $response = $this->getJson("/api/agendas/{$agenda->id}");
        $response->assertStatus(200);
        $response->assertJsonPath('data.submitter_name', 'Test Submitter');
    }

    public function test_non_authenticated_cannot_write_group_agendas()
    {
        $sb = $this->createServiceBody();
        $group = $this->createGroup($sb);

        $data = [
            'group_id' => $group->id,
            'meetings_per_week' => 2,
            'agenda_date' => '2026-06-01',
            'service_position' => 'GSR',
            'submitter_name' => 'Unauth Submitter',
            'new_comers' => 5,
            'recovery_atmosphere' => 'Great atmosphere',
            'trusted_servants' => 'Active trusted servants',
            'financial_issues' => 'None',
        ];

        $response = $this->postJson('/api/agendas', $data);
        $response->assertStatus(401);
    }

    public function test_authenticated_can_create_and_delete_group_agendas()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $sb = $this->createServiceBody();
        $group = $this->createGroup($sb);

        $data = [
            'group_id' => $group->id,
            'meetings_per_week' => 2,
            'agenda_date' => '2026-06-01',
            'service_position' => 'GSR',
            'submitter_name' => 'Auth Submitter',
            'new_comers' => 5,
            'recovery_atmosphere' => 'Great atmosphere',
            'trusted_servants' => 'Active trusted servants',
            'financial_issues' => 'None',
        ];

        $response = $this->postJson('/api/agendas', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('agendas', ['submitter_name' => 'Auth Submitter']);

        $agendaId = $response->json('data.id');

        $response = $this->deleteJson("/api/agendas/{$agendaId}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('agendas', ['submitter_name' => 'Auth Submitter']);
    }

    public function test_service_body_agendas_visibility_rules()
    {
        $sb1 = $this->createServiceBody(['ar_name' => 'منطقة 1', 'en_name' => 'SB 1']);
        $sb2 = $this->createServiceBody(['ar_name' => 'منطقة 2', 'en_name' => 'SB 2']);

        // 1. Draft agenda on SB1 (meeting date is today)
        $draftAgenda = ServiceBodyAgenda::create([
            'service_body_id' => $sb1->id,
            'agenda_date' => now()->toDateString(),
            'meeting_date' => now()->format('Y-m-d'),
            'status' => 'draft',
            'body' => [['headline' => 'Draft Head', 'content' => 'Draft Content']],
        ]);

        // 2. Approved and released agenda on SB2 (meeting date was 15 days ago, meeting date <= 10th of current month)
        // Let's set meeting_date to a date that makes it released.
        // If today is e.g. 22nd: meeting date of subMonth()->day(5) has release date subMonth()->day(10) which is in the past.
        $releasedDate = now()->subMonth()->day(5)->format('Y-m-d');
        $releasedAgenda = ServiceBodyAgenda::create([
            'service_body_id' => $sb2->id,
            'agenda_date' => now()->subMonth()->toDateString(),
            'meeting_date' => $releasedDate,
            'status' => 'approved',
            'body' => [['headline' => 'Released Head', 'content' => 'Released Content']],
        ]);

        // 3. Approved but unreleased agenda on SB2 (meeting date is 15th of current month, released on 10th of next month)
        $unreleasedDate = now()->day(15)->format('Y-m-d');
        $unreleasedAgenda = ServiceBodyAgenda::create([
            'service_body_id' => $sb2->id,
            'agenda_date' => now()->toDateString(),
            'meeting_date' => $unreleasedDate,
            'status' => 'approved',
            'body' => [['headline' => 'Unreleased Head', 'content' => 'Unreleased Content']],
        ]);

        // A. Public / Guest:
        // Can only see $releasedAgenda, NOT $draftAgenda, NOT $unreleasedAgenda
        $response = $this->getJson('/api/service-body-agendas');
        $response->assertStatus(200);
        $response->assertJsonFragment(['meeting_date' => $releasedDate]);
        $response->assertJsonMissing(['meeting_date' => $unreleasedDate]);

        $this->getJson("/api/service-body-agendas/{$releasedAgenda->id}")->assertStatus(200);
        $this->getJson("/api/service-body-agendas/{$draftAgenda->id}")->assertStatus(403);
        $this->getJson("/api/service-body-agendas/{$unreleasedAgenda->id}")->assertStatus(403);

        // B. RCM (ServiceBody role) of SB1:
        // Can see their own drafts ($draftAgenda), and released others ($releasedAgenda), but NOT others' unreleased ($unreleasedAgenda)
        $rcmUser = User::factory()->create(['service_body_id' => $sb1->id]);
        $rcmUser->assignRole('ServiceBody');
        Sanctum::actingAs($rcmUser);

        $response = $this->getJson('/api/service-body-agendas');
        $response->assertStatus(200);
        $response->assertJsonFragment(['meeting_date' => now()->format('Y-m-d')]); // own draft
        $response->assertJsonFragment(['meeting_date' => $releasedDate]); // other released
        $response->assertJsonMissing(['meeting_date' => $unreleasedDate]); // other unreleased

        $this->getJson("/api/service-body-agendas/{$draftAgenda->id}")->assertStatus(200);
        $this->getJson("/api/service-body-agendas/{$releasedAgenda->id}")->assertStatus(200);
        $this->getJson("/api/service-body-agendas/{$unreleasedAgenda->id}")->assertStatus(403);

        // C. Super Admin:
        // Can see everything
        $adminUser = User::factory()->create();
        $adminUser->assignRole('super admin');
        Sanctum::actingAs($adminUser);

        $response = $this->getJson('/api/service-body-agendas');
        $response->assertStatus(200);
        $response->assertJsonFragment(['meeting_date' => now()->format('Y-m-d')]); // own draft
        $response->assertJsonFragment(['meeting_date' => $releasedDate]); // other released
        $response->assertJsonFragment(['meeting_date' => $unreleasedDate]); // other unreleased

        $this->getJson("/api/service-body-agendas/{$draftAgenda->id}")->assertStatus(200);
        $this->getJson("/api/service-body-agendas/{$releasedAgenda->id}")->assertStatus(200);
        $this->getJson("/api/service-body-agendas/{$unreleasedAgenda->id}")->assertStatus(200);
    }
}
