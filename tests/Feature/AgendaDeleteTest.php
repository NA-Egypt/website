<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\City;
use App\Models\Day;
use App\Models\Group;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgendaDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $gsrUser;
    protected $agenda;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super admin']);
        Role::firstOrCreate(['name' => 'gsr']);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super admin');

        $this->gsrUser = User::factory()->create();
        $this->gsrUser->assignRole('gsr');

        $city = City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = Neighborhood::create(['ar_name' => 'المعادي', 'en_name' => 'Maadi', 'city_id' => $city->id]);
        $day = new Day();
        $day->name = 'Sunday';
        $day->save();
        $serviceBody = ServiceBody::create([
            'ar_name' => 'الهيئة الخدمية',
            'en_name' => 'Service Body',
            'day_id' => $day->id,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Location',
        ]);

        $group = Group::create([
            'ar_name' => 'مجموعة الأمل',
            'en_name' => 'Hope Group',
            'ar_gsr_name' => 'GSR Arabic',
            'en_gsr_name' => 'GSR English',
            'email' => 'group@example.com',
            'phone' => '12345678',
            'ar_address' => 'عنوان 1',
            'en_address' => 'Address 1',
            'location' => 'https://maps.google.com/1',
            'user_id' => $this->gsrUser->id,
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
            'group_type' => 'open',
            'slug' => 'hope-group',
        ]);

        $this->agenda = Agenda::create([
            'group_id' => $group->id,
            'meetings_per_week' => 3,
            'agenda_date' => now(),
            'service_position' => 'GSR',
            'submitter_name' => 'Ahmed',
            'recovery_atmosphere' => 'Good atmosphere',
            'trusted_servants' => 'Trusted Servants Info',
            'financial_issues' => 'None',
        ]);
    }

    public function test_super_admin_can_delete_single_agenda()
    {
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('agenda.destroy', $this->agenda->id));

        $response->assertRedirect(route('groups-agendas.archive'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('agendas', ['id' => $this->agenda->id]);
    }

    public function test_super_admin_can_bulk_delete_agendas()
    {
        $group = $this->agenda->group;
        $agenda2 = Agenda::create([
            'group_id' => $group->id,
            'meetings_per_week' => 2,
            'agenda_date' => now()->subMonth(),
            'service_position' => 'GSR',
            'submitter_name' => 'Ali',
            'recovery_atmosphere' => 'Great',
            'trusted_servants' => 'Servants',
            'financial_issues' => 'Ok',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('groups-agendas.bulk_delete'), [
                'agenda_ids' => [$this->agenda->id, $agenda2->id],
            ]);

        $response->assertRedirect(route('groups-agendas.archive'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('agendas', ['id' => $this->agenda->id]);
        $this->assertDatabaseMissing('agendas', ['id' => $agenda2->id]);
    }

    public function test_non_super_admin_cannot_delete_agenda()
    {
        $response = $this->actingAs($this->gsrUser)
            ->delete(route('agenda.destroy', $this->agenda->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('agendas', ['id' => $this->agenda->id]);
    }

    public function test_non_super_admin_cannot_bulk_delete_agendas()
    {
        $response = $this->actingAs($this->gsrUser)
            ->post(route('groups-agendas.bulk_delete'), [
                'agenda_ids' => [$this->agenda->id],
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('agendas', ['id' => $this->agenda->id]);
    }

    public function test_super_admin_can_bulk_export_agendas_pdf()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('groups-agendas.exportPdf'), [
                'agenda_ids' => [$this->agenda->id],
            ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_super_admin_with_gsr_role_can_bulk_export_agendas_pdf()
    {
        $this->superAdmin->assignRole('gsr');

        $response = $this->actingAs($this->superAdmin)
            ->post(route('groups-agendas.exportPdf'), [
                'agenda_ids' => [$this->agenda->id],
            ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
