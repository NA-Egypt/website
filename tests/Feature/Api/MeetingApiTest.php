<?php

namespace Tests\Feature\Api;

use App\Models\City;
use App\Models\Day;
use App\Models\DirectOnlineGroup;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\Option;
use App\Models\ServiceBody;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MeetingApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Day $saturday;
    private Day $friday;
    private Topic $stepTopic;
    private Option $openOption;
    private City $cairo;
    private Neighborhood $downtown;
    private ServiceBody $cairoServiceBody;
    private Group $hopeGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->saturday = Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $this->friday = Day::create(['ar_name' => 'الجمعة', 'en_name' => 'Friday']);

        $this->stepTopic = Topic::create(['ar_name' => 'خطوة أولى', 'en_name' => 'Step 1']);
        $this->openOption = Option::create(['ar_name' => 'مفتوح', 'en_name' => 'Open']);

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

        $this->cairoServiceBody = ServiceBody::create([
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
            'service_body_id' => $this->cairoServiceBody->id,
            'neighborhood_id' => $this->downtown->id,
            'user_id' => $this->user->id,
            'group_type' => 'in_person',
        ]);
    }

    public function test_public_can_list_meetings_with_complete_meeting_resource_schema(): void
    {
        $meeting = Meeting::create([
            'group_id' => $this->hopeGroup->id,
            'day_id' => $this->saturday->id,
            'topic_id' => $this->stepTopic->id,
            'start_time' => '19:30:00',
            'end_time' => '21:00:00',
            'type' => 'open',
            'lang' => 'arabic',
            'status' => 'available',
            'notes' => 'Wheelchair accessible',
        ]);

        $meeting->topics()->sync([$this->stepTopic->id]);
        $meeting->options()->sync([$this->openOption->id]);

        $response = $this->getJson('/api/v1/meetings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'day_id',
                        'group_id',
                        'direct_online_group_id',
                        'type',
                        'lang',
                        'status',
                        'start_time',
                        'end_time',
                        'formatted_start_time',
                        'formatted_end_time',
                        'duration',
                        'notes',
                        'recurrence',
                        'group_name_ar',
                        'group_name_en',
                        'group_type',
                        'address_ar',
                        'address_en',
                        'location_url',
                        'meeting_url',
                        'neighborhood_id',
                        'neighborhood_name_ar',
                        'neighborhood_name_en',
                        'city_id',
                        'city_name_ar',
                        'city_name_en',
                        'day',
                        'topics',
                        'options',
                    ]
                ]
            ])
            ->assertJsonPath('data.0.id', $meeting->id)
            ->assertJsonPath('data.0.group_name_ar', 'مجموعة الأمل')
            ->assertJsonPath('data.0.group_name_en', 'Hope Group')
            ->assertJsonPath('data.0.city_name_en', 'Cairo')
            ->assertJsonPath('data.0.location_url', 'https://maps.google.com/?q=30.0444,31.2357')
            ->assertJsonPath('data.0.meeting_url', 'https://maps.google.com/?q=30.0444,31.2357');
    }

    public function test_prioritized_url_resolution_fallback_hierarchy(): void
    {
        // Case 1: Group location fallback
        $meetingWithGroupLoc = Meeting::create([
            'group_id' => $this->hopeGroup->id,
            'day_id' => $this->saturday->id,
            'topic_id' => $this->stepTopic->id,
            'start_time' => '19:30:00',
            'end_time' => '21:00:00',
            'type' => 'open',
            'lang' => 'arabic',
            'status' => 'available',
        ]);

        $response1 = $this->getJson("/api/v1/meetings/{$meetingWithGroupLoc->id}");
        $response1->assertStatus(200)
            ->assertJsonPath('data.location_url', 'https://maps.google.com/?q=30.0444,31.2357')
            ->assertJsonPath('data.meeting_url', 'https://maps.google.com/?q=30.0444,31.2357');

        // Case 2: Direct Online Group meeting_url / zoom fallback
        $onlineGroup = DirectOnlineGroup::create([
            'ar_name' => 'مجموعة أونلاين',
            'en_name' => 'Online Group',
            'location' => 'https://zoom.us/j/987654321',
        ]);

        $onlineMeeting = Meeting::create([
            'direct_online_group_id' => $onlineGroup->id,
            'day_id' => $this->friday->id,
            'topic_id' => $this->stepTopic->id,
            'start_time' => '20:00:00',
            'end_time' => '21:30:00',
            'type' => 'open',
            'lang' => 'arabic',
            'status' => 'available',
        ]);

        $response2 = $this->getJson("/api/v1/meetings/{$onlineMeeting->id}");
        $response2->assertStatus(200)
            ->assertJsonPath('data.group_type', 'online')
            ->assertJsonPath('data.location_url', 'https://zoom.us/j/987654321')
            ->assertJsonPath('data.meeting_url', 'https://zoom.us/j/987654321');
    }

    public function test_meetings_filtering_by_day_and_city(): void
    {
        Meeting::create([
            'group_id' => $this->hopeGroup->id,
            'day_id' => $this->saturday->id,
            'topic_id' => $this->stepTopic->id,
            'start_time' => '19:30:00',
            'end_time' => '21:00:00',
            'type' => 'open',
            'lang' => 'arabic',
            'status' => 'available',
        ]);

        // Filter by Saturday
        $responseSat = $this->getJson('/api/v1/meetings?day=Saturday');
        $responseSat->assertStatus(200);

        // Filter by Cairo
        $responseCairo = $this->getJson('/api/v1/meetings?city=Cairo');
        $responseCairo->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_create_or_update_or_delete_meeting(): void
    {
        $storeResponse = $this->postJson('/api/v1/meetings', [
            'group_id' => $this->hopeGroup->id,
            'day_id' => $this->saturday->id,
            'start_time' => '19:30:00',
            'end_time' => '21:00:00',
            'type' => 'open',
            'lang' => 'arabic',
            'status' => 'available',
        ]);
        $storeResponse->assertStatus(401);

        $meeting = Meeting::create([
            'group_id' => $this->hopeGroup->id,
            'day_id' => $this->saturday->id,
            'topic_id' => $this->stepTopic->id,
            'start_time' => '19:30:00',
            'end_time' => '21:00:00',
            'type' => 'open',
            'lang' => 'arabic',
            'status' => 'available',
        ]);

        $updateResponse = $this->putJson("/api/v1/meetings/{$meeting->id}", ['notes' => 'Updated']);
        $updateResponse->assertStatus(401);

        $deleteResponse = $this->deleteJson("/api/v1/meetings/{$meeting->id}");
        $deleteResponse->assertStatus(401);
    }

    public function test_authenticated_user_can_create_update_and_delete_meeting(): void
    {
        Sanctum::actingAs($this->user);

        // Store
        $storeResponse = $this->postJson('/api/v1/meetings', [
            'group_id' => $this->hopeGroup->id,
            'day_id' => $this->saturday->id,
            'start_time' => '18:00:00',
            'end_time' => '19:30:00',
            'type' => 'closed',
            'lang' => 'arabic',
            'status' => 'available',
            'notes' => 'Initial notes',
            'topics' => [$this->stepTopic->id],
            'options' => [$this->openOption->id],
        ]);

        $storeResponse->assertStatus(201)
            ->assertJsonPath('data.type', 'closed')
            ->assertJsonPath('data.notes', 'Initial notes');

        $meetingId = $storeResponse->json('data.id');

        // Update
        $updateResponse = $this->putJson("/api/v1/meetings/{$meetingId}", [
            'notes' => 'Updated notes',
            'type' => 'open',
        ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.notes', 'Updated notes')
            ->assertJsonPath('data.type', 'open');

        // Delete
        $deleteResponse = $this->deleteJson("/api/v1/meetings/{$meetingId}");
        $deleteResponse->assertStatus(204);

        $this->assertDatabaseMissing('meetings', ['id' => $meetingId]);
    }

    public function test_meeting_store_validates_required_fields(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/meetings', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['group_id', 'day_id', 'start_time', 'end_time', 'type', 'lang', 'status']);
    }
}
