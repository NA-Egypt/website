<?php

namespace Tests\Feature\Api;

use App\Models\Agenda;
use App\Models\City;
use App\Models\Day;
use App\Models\Group;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\ServiceBodyAgenda;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgendaApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $superAdmin;
    private Day $saturday;
    private City $cairo;
    private Neighborhood $downtown;
    private ServiceBody $cairoArea;
    private Group $hopeGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $superAdminRole = Role::firstOrCreate(['name' => 'super admin']);
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole($superAdminRole);

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
    }

    public function test_group_agendas_public_index_and_authenticated_crud(): void
    {
        $agenda = Agenda::create([
            'group_id' => $this->hopeGroup->id,
            'agenda_date' => '2026-08-01',
            'service_position' => 'GSR',
            'submitter_name' => 'Ahmed Ali',
            'meetings_per_week' => 4,
            'new_comers' => 5,
            'open_positions' => 'Treasurer',
            'recovery_atmosphere' => 'Good',
            'trusted_servants' => 'Active',
            'financial_issues' => 'None',
            'other_topics' => [
                ['title' => 'Literature', 'content' => 'Need more books']
            ],
        ]);

        // Public index & show
        $this->getJson('/api/v1/agendas')->assertStatus(200);
        $this->getJson("/api/v1/agendas/{$agenda->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.service_position', 'GSR');

        // Authenticated CRUD
        Sanctum::actingAs($this->user);

        $createRes = $this->postJson('/api/v1/agendas', [
            'group_id' => $this->hopeGroup->id,
            'agenda_date' => '2026-09-01',
            'service_position' => 'Alt. GSR',
            'submitter_name' => 'Jane Smith',
            'meetings_per_week' => 3,
            'new_comers' => 2,
            'recovery_atmosphere' => 'Welcoming',
            'trusted_servants' => 'Full',
            'financial_issues' => 'Healthy',
            'other_topics' => [
                ['title' => 'Workshop', 'content' => 'Traditions workshop planned']
            ],
        ]);
        $createRes->assertStatus(201);
        $newId = $createRes->json('data.id');

        $updateRes = $this->putJson("/api/v1/agendas/{$newId}", [
            'group_id' => $this->hopeGroup->id,
            'agenda_date' => '2026-09-01',
            'service_position' => 'GSR',
            'recovery_atmosphere' => 'Excellent',
            'trusted_servants' => 'Full',
            'financial_issues' => 'Healthy',
        ]);
        $updateRes->assertStatus(200);

        $this->deleteJson("/api/v1/agendas/{$newId}")->assertStatus(204);
    }

    public function test_service_body_agendas_visibility_and_crud(): void
    {
        // 1. Draft agenda is hidden from public
        $draftAgenda = ServiceBodyAgenda::create([
            'service_body_id' => $this->cairoArea->id,
            'agenda_date' => '2026-09-01',
            'meeting_date' => '2026-09-05',
            'body' => [['headline' => 'Draft topic', 'content' => 'Under discussion']],
            'status' => 'draft',
            'is_exceptional' => false,
        ]);

        $publicRes = $this->getJson('/api/v1/service-body-agendas');
        $publicRes->assertStatus(200);
        $this->assertCount(0, $publicRes->json('data'));

        // 2. Exceptional approved agenda is visible to public
        $exceptionalAgenda = ServiceBodyAgenda::create([
            'service_body_id' => $this->cairoArea->id,
            'agenda_date' => '2026-09-01',
            'meeting_date' => '2026-09-05',
            'body' => [['headline' => 'Regional Assembly', 'content' => 'Public vote']],
            'status' => 'approved',
            'is_exceptional' => true,
        ]);

        $publicRes2 = $this->getJson('/api/v1/service-body-agendas');
        $publicRes2->assertStatus(200);
        $this->assertCount(1, $publicRes2->json('data'));

        // 3. Super Admin can create, view all drafts, and manage agendas
        Sanctum::actingAs($this->superAdmin);

        $adminListRes = $this->getJson('/api/v1/service-body-agendas');
        $adminListRes->assertStatus(200);
        $this->assertCount(2, $adminListRes->json('data'));

        $createRes = $this->postJson('/api/v1/service-body-agendas', [
            'service_body_id' => $this->cairoArea->id,
            'meeting_date' => '2026-10-05',
            'sections' => [
                ['headline' => 'October Agenda', 'content' => 'Agenda items']
            ],
            'status' => 'draft',
        ]);
        $createRes->assertStatus(201);
        $newSbAgendaId = $createRes->json('data.id');

        $this->deleteJson("/api/v1/service-body-agendas/{$newSbAgendaId}")->assertStatus(204);
    }
}
