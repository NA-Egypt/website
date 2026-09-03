<?php

namespace Tests\Feature\Api;

use App\Models\CalendarEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CalendarEventApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    public function test_public_can_list_calendar_events(): void
    {
        CalendarEvent::create([
            'user_id' => $this->user->id,
            'title' => 'First Event',
            'start' => Carbon::now()->addDays(2),
            'end' => Carbon::now()->addDays(2)->addHours(2),
            'description' => 'First event description',
            'color' => '#00698f',
        ]);

        CalendarEvent::create([
            'user_id' => $this->user->id,
            'title' => 'Second Event',
            'start' => Carbon::now()->addDays(5),
            'end' => Carbon::now()->addDays(5)->addHours(2),
            'description' => 'Second event description',
            'color' => '#ff0000',
        ]);

        $response = $this->getJson('/api/v1/calendar-events');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'start',
                        'end',
                        'description',
                        'color',
                        'recurrence',
                        'formatted_recurrence',
                        'is_featured',
                    ]
                ]
            ]);
    }

    public function test_public_can_filter_calendar_events_by_date_range_with_recurrence(): void
    {
        $baseDate = Carbon::create(2026, 9, 1, 10, 0, 0);

        CalendarEvent::create([
            'user_id' => $this->user->id,
            'title' => 'Weekly Service Meeting',
            'start' => $baseDate,
            'end' => $baseDate->copy()->addHours(2),
            'description' => 'Weekly committee',
            'recurrence' => ['weekly'],
            'color' => '#00698f',
        ]);

        $response = $this->getJson('/api/v1/calendar-events?start=2026-09-01&end=2026-09-30');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertGreaterThanOrEqual(4, count($data));
    }

    public function test_public_can_get_single_calendar_event(): void
    {
        $event = CalendarEvent::create([
            'user_id' => $this->user->id,
            'title' => 'Regional Assembly',
            'start' => Carbon::now()->addDays(3),
            'end' => Carbon::now()->addDays(3)->addHours(4),
            'description' => 'Assembly details',
            'organizer' => 'Egypt RSC',
            'location' => 'Cairo',
            'color' => '#00698f',
        ]);

        $response = $this->getJson("/api/v1/calendar-events/{$event->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $event->id,
                    'title' => 'Regional Assembly',
                    'organizer' => 'Egypt RSC',
                    'location' => 'Cairo',
                ]
            ]);
    }

    public function test_unauthenticated_user_cannot_create_calendar_event(): void
    {
        $payload = [
            'title' => 'Unauthorized Event',
            'start' => Carbon::now()->addDays(1)->toIso8601String(),
            'end' => Carbon::now()->addDays(1)->addHours(2)->toIso8601String(),
        ];

        $response = $this->postJson('/api/v1/calendar-events', $payload);
        $response->assertStatus(401);
    }

    public function test_unauthorized_user_cannot_create_calendar_event(): void
    {
        $plainUser = User::factory()->create();
        Sanctum::actingAs($plainUser);

        $payload = [
            'title' => 'Unauthorized Event',
            'start' => Carbon::now()->addDays(1)->toIso8601String(),
            'end' => Carbon::now()->addDays(1)->addHours(2)->toIso8601String(),
        ];

        $response = $this->postJson('/api/v1/calendar-events', $payload);
        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_create_calendar_event(): void
    {
        Sanctum::actingAs($this->user);

        $payload = [
            'title' => 'New Regional Workshop',
            'start' => Carbon::now()->addDays(10)->toIso8601String(),
            'end' => Carbon::now()->addDays(10)->addHours(3)->toIso8601String(),
            'description' => 'PR Workshop',
            'color' => '#10b981',
            'organizer' => 'PR Subcommittee',
            'location' => 'Online & Cairo',
            'recurrence' => ['once'],
            'is_featured' => true,
        ];

        $response = $this->postJson('/api/v1/calendar-events', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'New Regional Workshop',
                    'organizer' => 'PR Subcommittee',
                    'location' => 'Online & Cairo',
                    'is_featured' => true,
                ]
            ]);

        $this->assertDatabaseHas('calendar_events', [
            'title' => 'New Regional Workshop',
            'organizer' => 'PR Subcommittee',
            'is_featured' => 1,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/calendar-events', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'start', 'end']);
    }

    public function test_authenticated_user_can_update_calendar_event(): void
    {
        Sanctum::actingAs($this->user);

        $event = CalendarEvent::create([
            'user_id' => $this->user->id,
            'title' => 'Initial Title',
            'start' => Carbon::now()->addDays(1),
            'end' => Carbon::now()->addDays(1)->addHours(2),
            'description' => 'Initial Desc',
            'color' => '#00698f',
        ]);

        $response = $this->putJson("/api/v1/calendar-events/{$event->id}", [
            'title' => 'Updated Title',
            'is_featured' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $event->id,
                    'title' => 'Updated Title',
                    'is_featured' => true,
                ]
            ]);

        $this->assertDatabaseHas('calendar_events', [
            'id' => $event->id,
            'title' => 'Updated Title',
            'is_featured' => 1,
        ]);
    }

    public function test_authenticated_user_can_delete_calendar_event(): void
    {
        Sanctum::actingAs($this->user);

        $event = CalendarEvent::create([
            'user_id' => $this->user->id,
            'title' => 'To Be Deleted',
            'start' => Carbon::now()->addDays(1),
            'end' => Carbon::now()->addDays(1)->addHours(2),
            'color' => '#00698f',
        ]);

        $response = $this->deleteJson("/api/v1/calendar-events/{$event->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('calendar_events', ['id' => $event->id]);
    }
}

