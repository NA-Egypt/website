<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\ServiceBody;
use App\Models\ServiceCommittee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CalendarEventEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $rscUser;
    protected User $committeeUser;
    protected ServiceCommittee $prCommittee;
    protected ServiceCommittee $itWorkgroup;
    protected ServiceBody $cairoAsc;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        Permission::firstOrCreate(['name' => 'create calendar events', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage calendar events', 'guard_name' => 'web']);

        $superAdminRole = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $rscRole = Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);
        $committeesRole = Role::firstOrCreate(['name' => 'Committees', 'guard_name' => 'web']);

        $superAdminRole->givePermissionTo(['create calendar events', 'manage calendar events']);
        $rscRole->givePermissionTo(['create calendar events', 'manage calendar events']);
        $committeesRole->givePermissionTo(['create calendar events']);

        $this->superAdmin = User::factory()->create(['name' => 'Super Admin']);
        $this->superAdmin->assignRole($superAdminRole);

        $this->rscUser = User::factory()->create(['name' => 'RSC Officer']);
        $this->rscUser->assignRole($rscRole);

        $this->committeeUser = User::factory()->create(['name' => 'PR Chair']);
        $this->committeeUser->assignRole($committeesRole);

        $rscModel = new ServiceCommittee([
            'ar_name' => 'لجنة خدمة إقليم مصر',
            'en_name' => 'Egypt Regional Service Committee',
            'status' => 'active',
        ]);
        $rscModel->id = 83;
        $rscModel->save();

        $this->prCommittee = ServiceCommittee::create([
            'ar_name' => 'لجنة العلاقات العامة',
            'en_name' => 'Public Relations Committee',
            'status' => 'active',
            'user_id' => $this->committeeUser->id,
        ]);

        $this->itWorkgroup = ServiceCommittee::create([
            'parent_id' => 83,
            'ar_name' => 'مجموعة تقنية المعلومات',
            'en_name' => 'Information Technology Workgroup',
            'status' => 'active',
        ]);

        $day = \App\Models\Day::create([
            'ar_name' => 'الجمعة',
            'en_name' => 'Friday',
        ]);

        $this->cairoAsc = ServiceBody::create([
            'ar_name' => 'منطقة الوحدة',
            'en_name' => 'Al Wehda ASC',
            'day_id' => $day->id,
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
            'location' => 'Cairo',
        ]);
    }

    public function test_organizer_is_required_and_must_belong_to_committees_workgroups_or_service_bodies(): void
    {
        $basePayload = [
            'title' => 'Workshop Event',
            'start' => Carbon::now()->addDays(3)->toIso8601String(),
            'end' => Carbon::now()->addDays(3)->addHours(2)->toIso8601String(),
            'color' => '#00698f',
        ];

        // Missing organizer fails with 422
        $this->actingAs($this->committeeUser)
            ->postJson(route('web-calendar-events.store'), $basePayload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['organizer']);

        // Arbitrary free-text organizer not in official catalog fails with 422
        $this->actingAs($this->committeeUser)
            ->postJson(route('web-calendar-events.store'), array_merge($basePayload, [
                'organizer' => 'Random Unofficial Organizer',
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['organizer']);

        // Valid Committee name succeeds
        $this->actingAs($this->committeeUser)
            ->postJson(route('web-calendar-events.store'), array_merge($basePayload, [
                'title' => 'PR Event',
                'organizer' => 'لجنة العلاقات العامة',
            ]))
            ->assertStatus(201);

        // Valid Workgroup name succeeds
        $this->actingAs($this->committeeUser)
            ->postJson(route('web-calendar-events.store'), array_merge($basePayload, [
                'title' => 'IT Event',
                'organizer' => 'Information Technology Workgroup',
            ]))
            ->assertStatus(201);

        // Valid Service Body name succeeds
        $this->actingAs($this->committeeUser)
            ->postJson(route('web-calendar-events.store'), array_merge($basePayload, [
                'title' => 'ASC Event',
                'organizer' => 'منطقة الوحدة',
            ]))
            ->assertStatus(201);
    }

    public function test_organizer_catalog_groups_and_user_default_preselection(): void
    {
        $catalogForPr = CalendarEvent::getOrganizerCatalog($this->committeeUser, 'ar');
        $this->assertEquals('لجنة العلاقات العامة', $catalogForPr['default_organizer']);
        $this->assertCount(3, $catalogForPr['groups']);

        $catalogForRsc = CalendarEvent::getOrganizerCatalog($this->rscUser, 'en');
        $this->assertEquals('Egypt Regional Service Committee', $catalogForRsc['default_organizer']);
    }

    public function test_location_link_classification_for_zoom_maps_and_text(): void
    {
        $zoomEvent = new CalendarEvent(['location' => 'https://us02web.zoom.us/j/89123456789?pwd=abc']);
        $zoomInfo = $zoomEvent->location_link_info;
        $this->assertTrue($zoomInfo['is_link']);
        $this->assertEquals('zoom', $zoomInfo['type']);
        $this->assertEquals('https://us02web.zoom.us/j/89123456789?pwd=abc', $zoomInfo['url']);

        $mapEvent = new CalendarEvent(['location' => 'https://maps.app.goo.gl/xyz123']);
        $mapInfo = $mapEvent->location_link_info;
        $this->assertTrue($mapInfo['is_link']);
        $this->assertEquals('map', $mapInfo['type']);
        $this->assertEquals('https://maps.app.goo.gl/xyz123', $mapInfo['url']);

        $googleMapsEvent = new CalendarEvent(['location' => 'https://www.google.com/maps/place/Cairo']);
        $this->assertEquals('map', $googleMapsEvent->location_link_info['type']);

        $plainEvent = new CalendarEvent(['location' => 'Nasr City Community Hall, Cairo']);
        $plainInfo = $plainEvent->location_link_info;
        $this->assertFalse($plainInfo['is_link']);
        $this->assertEquals('text', $plainInfo['type']);
        $this->assertNull($plainInfo['url']);
    }

    public function test_only_one_event_can_be_hero_at_a_time_and_permissions_are_enforced(): void
    {
        // Regular committee user cannot set is_hero = true
        $res = $this->actingAs($this->committeeUser)
            ->postJson(route('web-calendar-events.store'), [
                'title' => 'Committee Workshop',
                'start' => Carbon::now()->addDays(2)->toIso8601String(),
                'end' => Carbon::now()->addDays(2)->addHours(2)->toIso8601String(),
                'organizer' => 'Public Relations Committee',
                'color' => '#00698f',
                'is_hero' => true,
            ])
            ->assertStatus(201);

        $committeeEventId = $res->json('data.id');
        $this->assertDatabaseHas('calendar_events', [
            'id' => $committeeEventId,
            'is_hero' => 0,
        ]);

        // RSC user CAN set Event 1 as Hero
        $res1 = $this->actingAs($this->rscUser)
            ->postJson(route('web-calendar-events.store'), [
                'title' => 'Annual Convention Hero 1',
                'start' => Carbon::now()->addDays(15)->toIso8601String(),
                'end' => Carbon::now()->addDays(17)->toIso8601String(),
                'organizer' => 'Egypt Regional Service Committee',
                'color' => '#00698f',
                'is_hero' => true,
            ])
            ->assertStatus(201);

        $hero1Id = $res1->json('data.id');
        $this->assertDatabaseHas('calendar_events', [
            'id' => $hero1Id,
            'is_hero' => 1,
        ]);

        // Super Admin sets Event 2 as Hero -> Event 1 is automatically unset
        $res2 = $this->actingAs($this->superAdmin)
            ->postJson(route('web-calendar-events.store'), [
                'title' => 'Regional Service Symposium Hero 2',
                'start' => Carbon::now()->addDays(25)->toIso8601String(),
                'end' => Carbon::now()->addDays(26)->toIso8601String(),
                'organizer' => 'Egypt Regional Service Committee',
                'color' => '#28a745',
                'is_hero' => true,
            ])
            ->assertStatus(201);

        $hero2Id = $res2->json('data.id');
        $this->assertDatabaseHas('calendar_events', [
            'id' => $hero1Id,
            'is_hero' => 0,
        ]);
        $this->assertDatabaseHas('calendar_events', [
            'id' => $hero2Id,
            'is_hero' => 1,
        ]);
        $this->assertEquals(1, CalendarEvent::where('is_hero', true)->count());
    }

    public function test_homepage_renders_dynamic_hero_banner_and_hides_when_none_selected(): void
    {
        // When no hero event exists, neither the hero banner nor the old static convention title is shown
        $responseEmpty = $this->get('/');
        $responseEmpty->assertStatus(200);
        $responseEmpty->assertDontSee('id="homepage-hero-event-banner"', false);
        $responseEmpty->assertDontSee('المؤتمر السنوي لزمالة المدمنين المجهولين بمصر (مسار يجمعنا)');

        // Create a hero event (even in the past, it stays until unchecked)
        $hero = CalendarEvent::create([
            'title' => 'المؤتمر السنوي الحادي والثلاثون بمدينة الإسكندرية',
            'description' => 'وصف المؤتمر السنوي في الإسكندرية',
            'start' => Carbon::now()->subDays(5),
            'end' => Carbon::now()->subDays(2),
            'organizer' => 'لجنة خدمة إقليم مصر',
            'location' => 'https://maps.app.goo.gl/AlexandriaConventionVenue',
            'color' => '#00698f',
            'is_hero' => true,
            'user_id' => $this->rscUser->id,
        ]);

        $responseWithHero = $this->get('/');
        $responseWithHero->assertStatus(200);
        $responseWithHero->assertSee('id="homepage-hero-event-banner"', false);
        $responseWithHero->assertSee('المؤتمر السنوي الحادي والثلاثون بمدينة الإسكندرية');
        $responseWithHero->assertSee('وصف المؤتمر السنوي في الإسكندرية');
        $responseWithHero->assertSee('لجنة خدمة إقليم مصر');
        $responseWithHero->assertSee('https://maps.app.goo.gl/AlexandriaConventionVenue');

        // Uncheck is_hero -> hero banner container and hero-exclusive organizer badge disappear from homepage
        $hero->update(['is_hero' => false]);

        $responseAfterUncheck = $this->get('/');
        $responseAfterUncheck->assertStatus(200);
        $responseAfterUncheck->assertDontSee('id="homepage-hero-event-banner"', false);
        $responseAfterUncheck->assertDontSee('لجنة خدمة إقليم مصر');
    }
}
