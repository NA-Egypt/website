<?php

namespace Tests\Feature\Api;

use App\Models\City;
use App\Models\Day;
use App\Models\Group;
use App\Models\Neighborhood;
use App\Models\Option;
use App\Models\ScMeeting;
use App\Models\ServiceBody;
use App\Models\ServiceCommittee;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DirectoryApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Day $saturday;
    private City $cairo;
    private Neighborhood $downtown;
    private ServiceBody $cairoArea;

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
    }

    public function test_groups_public_index_and_authenticated_crud(): void
    {
        $group = Group::create([
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

        // Public index
        $indexRes = $this->getJson('/api/v1/groups');
        $indexRes->assertStatus(200)
            ->assertJsonPath('data.0.ar_name', 'مجموعة الأمل');

        // Public show
        $showRes = $this->getJson("/api/v1/groups/{$group->id}");
        $showRes->assertStatus(200)
            ->assertJsonPath('data.en_name', 'Hope Group');

        // Unauthenticated write fails
        $this->postJson('/api/v1/groups', ['ar_name' => 'New Group'])->assertStatus(401);

        // Authenticated write
        Sanctum::actingAs($this->user);
        $createRes = $this->postJson('/api/v1/groups', [
            'ar_name' => 'مجموعة السلام',
            'en_name' => 'Peace Group',
            'ar_gsr_name' => 'سامي',
            'en_gsr_name' => 'Samy',
            'phone' => '+201111111111',
            'location' => 'https://maps.google.com/?q=30.0,31.0',
            'ar_address' => 'القاهرة',
            'en_address' => 'Cairo',
            'service_body_id' => $this->cairoArea->id,
            'neighborhood_id' => $this->downtown->id,
            'group_type' => 'in_person',
        ]);
        $createRes->assertStatus(201);
        $newGroupId = $createRes->json('data.id');

        // Update
        $updateRes = $this->putJson("/api/v1/groups/{$newGroupId}", [
            'en_name' => 'Peace Group Updated',
        ]);
        $updateRes->assertStatus(200)
            ->assertJsonPath('data.en_name', 'Peace Group Updated');

        // Destroy
        $deleteRes = $this->deleteJson("/api/v1/groups/{$newGroupId}");
        $deleteRes->assertStatus(204);
    }

    public function test_cities_public_index_and_authenticated_crud(): void
    {
        // Public index
        $this->getJson('/api/v1/cities')->assertStatus(200);

        // Public show
        $this->getJson("/api/v1/cities/{$this->cairo->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.en_name', 'Cairo');

        // Authenticated CRUD
        Sanctum::actingAs($this->user);

        $createRes = $this->postJson('/api/v1/cities', [
            'ar_name' => 'الإسكندرية',
            'en_name' => 'Alexandria',
            'latitude' => 31.2001,
            'longitude' => 29.9187,
        ]);
        $createRes->assertStatus(201);
        $cityId = $createRes->json('data.id');

        $updateRes = $this->putJson("/api/v1/cities/{$cityId}", [
            'en_name' => 'Alexandria City',
        ]);
        $updateRes->assertStatus(200)
            ->assertJsonPath('data.en_name', 'Alexandria City');

        $this->deleteJson("/api/v1/cities/{$cityId}")->assertStatus(204);
    }

    public function test_neighborhoods_public_index_and_authenticated_crud(): void
    {
        // Public index
        $this->getJson('/api/v1/neighborhoods')->assertStatus(200);

        // Authenticated CRUD
        Sanctum::actingAs($this->user);

        $createRes = $this->postJson('/api/v1/neighborhoods', [
            'ar_name' => 'الزمالك',
            'en_name' => 'Zamalek',
            'city_id' => $this->cairo->id,
        ]);
        $createRes->assertStatus(201);
        $neighId = $createRes->json('data.id');

        $this->putJson("/api/v1/neighborhoods/{$neighId}", ['en_name' => 'Zamalek Island'])
            ->assertStatus(200)
            ->assertJsonPath('data.en_name', 'Zamalek Island');

        $this->deleteJson("/api/v1/neighborhoods/{$neighId}")->assertStatus(204);
    }

    public function test_topics_and_options_public_index_and_crud(): void
    {
        $topic = Topic::create(['ar_name' => 'خطوة', 'en_name' => 'Step']);
        $option = Option::create(['ar_name' => 'مغلق', 'en_name' => 'Closed']);

        // Public index
        $this->getJson('/api/v1/topics')->assertStatus(200);
        $this->getJson('/api/v1/options')->assertStatus(200);

        // Authenticated CRUD
        Sanctum::actingAs($this->user);

        $topicRes = $this->postJson('/api/v1/topics', ['ar_name' => 'تقليد', 'en_name' => 'Tradition']);
        $topicRes->assertStatus(201);
        $topicId = $topicRes->json('data.id');

        $this->deleteJson("/api/v1/topics/{$topicId}")->assertStatus(204);

        $optRes = $this->postJson('/api/v1/options', ['ar_name' => 'رجال فقط', 'en_name' => 'Men Only']);
        $optRes->assertStatus(201);
        $optId = $optRes->json('data.id');

        $this->deleteJson("/api/v1/options/{$optId}")->assertStatus(204);
    }

    public function test_service_bodies_and_service_committees_and_sc_meetings_crud(): void
    {
        // Public index
        $this->getJson('/api/v1/service-bodies')->assertStatus(200);
        $this->getJson('/api/v1/service-committees')->assertStatus(200);
        $this->getJson('/api/v1/sc-meetings')->assertStatus(200);

        // Authenticated CRUD
        Sanctum::actingAs($this->user);

        // Service Committee
        $scRes = $this->postJson('/api/v1/service-committees', [
            'ar_name' => 'لجنة المستشفيات والمؤسسات',
            'en_name' => 'H&I Committee',
            'ar_address' => 'وسط البلد، القاهرة',
            'en_address' => 'Downtown, Cairo',
            'location' => 'https://maps.google.com/?q=30.0,31.0',
        ]);
        $scRes->assertStatus(201);
        $scId = $scRes->json('data.id');

        // SC Meeting
        $scMeetingRes = $this->postJson('/api/v1/sc-meetings', [
            'service_committee_id' => $scId,
            'week_number' => 1,
            'day_id' => $this->saturday->id,
            'time' => '17:00:00',
            'notes' => 'Room A',
        ]);
        $scMeetingRes->assertStatus(201);

        $this->deleteJson("/api/v1/service-committees/{$scId}")->assertStatus(204);
    }
}
