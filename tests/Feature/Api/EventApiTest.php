<?php

namespace Tests\Feature\Api;

use App\Models\Day;
use App\Models\Event;
use App\Models\ServiceBody;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventApiTest extends TestCase
{
    use RefreshDatabase;

    private Day $day;
    private ServiceBody $serviceBody;

    protected function setUp(): void
    {
        parent::setUp();

        $this->day = Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $this->serviceBody = ServiceBody::create([
            'ar_name' => 'لجنة مصر',
            'en_name' => 'Egypt RSC',
            'type' => 'rsc',
            'location' => 'Cairo',
            'day_id' => $this->day->id,
            'date' => '2026-01-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
        ]);
    }

    public function test_public_can_list_events(): void
    {
        Event::create([
            'name' => 'Unity Convention',
            'description' => 'Annual convention gathering',
            'date' => '2026-10-15',
            'service_body_id' => $this->serviceBody->id,
            'day_id' => $this->day->id,
        ]);

        $response = $this->getJson('/api/v1/events');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'date',
                        'service_body_id',
                        'service_body',
                        'day_id',
                        'day',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    }

    public function test_public_can_show_event(): void
    {
        $event = Event::create([
            'name' => 'Workshop 2026',
            'description' => 'Public relations workshop',
            'date' => '2026-11-20',
            'service_body_id' => $this->serviceBody->id,
            'day_id' => $this->day->id,
        ]);

        $response = $this->getJson("/api/v1/events/{$event->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $event->id,
                    'name' => 'Workshop 2026',
                    'description' => 'Public relations workshop',
                    'date' => '2026-11-20',
                ]
            ]);
    }

    public function test_unauthenticated_user_cannot_create_event(): void
    {
        $payload = [
            'name' => 'Unauthorized Event',
            'description' => 'Desc',
            'date' => '2026-12-01',
            'service_body_id' => $this->serviceBody->id,
            'day_id' => $this->day->id,
        ];

        $response = $this->postJson('/api/v1/events', $payload);
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_event(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'name' => 'New Regional Event',
            'description' => 'Event details description',
            'date' => '2026-12-15',
            'service_body_id' => $this->serviceBody->id,
            'day_id' => $this->day->id,
        ];

        $response = $this->postJson('/api/v1/events', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'New Regional Event',
                    'date' => '2026-12-15',
                    'service_body_id' => $this->serviceBody->id,
                    'day_id' => $this->day->id,
                ]
            ]);

        $this->assertDatabaseHas('events', [
            'name' => 'New Regional Event',
            'service_body_id' => $this->serviceBody->id,
            'day_id' => $this->day->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/events', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description', 'date', 'service_body_id', 'day_id']);
    }

    public function test_authenticated_user_can_update_event(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $event = Event::create([
            'name' => 'Old Event Name',
            'description' => 'Old Desc',
            'date' => '2026-10-10',
            'service_body_id' => $this->serviceBody->id,
            'day_id' => $this->day->id,
        ]);

        $response = $this->putJson("/api/v1/events/{$event->id}", [
            'name' => 'Updated Event Name',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $event->id,
                    'name' => 'Updated Event Name',
                ]
            ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'name' => 'Updated Event Name',
        ]);
    }

    public function test_authenticated_user_can_delete_event(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $event = Event::create([
            'name' => 'Event To Delete',
            'description' => 'Delete me',
            'date' => '2026-10-10',
            'service_body_id' => $this->serviceBody->id,
            'day_id' => $this->day->id,
        ]);

        $response = $this->deleteJson("/api/v1/events/{$event->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }
}
