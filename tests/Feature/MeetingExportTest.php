<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Day;
use App\Models\ServiceBody;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\MeetingExportWizard;

class MeetingExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }

    public function test_download_export_by_service_body()
    {
        $day = Day::first(); // automatically seeded by migration

        $sb = ServiceBody::create([
            'ar_name' => 'منطقة/منتدى',
            'en_name' => 'Service Body',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Location',
        ]);

        $city = City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = Neighborhood::create([
            'ar_name' => 'حي',
            'en_name' => 'Neighborhood',
            'city_id' => $city->id,
        ]);

        $user = User::factory()->create();

        $group = Group::create([
            'ar_name' => 'المجموعة',
            'en_name' => 'Group',
            'ar_gsr_name' => 'GSR',
            'en_gsr_name' => 'GSR',
            'email' => 'g@example.com',
            'phone' => '12345',
            'location' => 'https://maps.google.com',
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'group_type' => 'open',
            'service_body_id' => $sb->id,
            'neighborhood_id' => $neighborhood->id,
            'user_id' => $user->id,
        ]);

        $topic = Topic::create([
            'ar_name' => 'الموضوع',
            'en_name' => 'Topic',
        ]);

        $meeting = Meeting::create([
            'group_id' => $group->id,
            'day_id' => $day->id,
            'topic_id' => $topic->id,
            'start_time' => '18:00:00',
            'end_time' => '19:30:00',
            'type' => 'open',
            'status' => 'available',
        ]);

        $response = $this->get(route('meetings.export.download', [
            'export_type' => 'service_bodies',
            'service_bodies' => [$sb->id],
            'fields' => ['topic', 'time'],
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_download_export_by_cities()
    {
        $day = Day::first(); // automatically seeded by migration

        $sb = ServiceBody::create([
            'ar_name' => 'منطقة/منتدى',
            'en_name' => 'Service Body',
            'day_id' => $day->id,
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location' => 'Location',
        ]);

        $city = City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = Neighborhood::create([
            'ar_name' => 'حي',
            'en_name' => 'Neighborhood',
            'city_id' => $city->id,
        ]);

        $user = User::factory()->create();

        $group = Group::create([
            'ar_name' => 'المجموعة',
            'en_name' => 'Group',
            'ar_gsr_name' => 'GSR',
            'en_gsr_name' => 'GSR',
            'email' => 'g@example.com',
            'phone' => '12345',
            'location' => 'https://maps.google.com',
            'ar_address' => 'العنوان',
            'en_address' => 'Address',
            'group_type' => 'open',
            'service_body_id' => $sb->id,
            'neighborhood_id' => $neighborhood->id,
            'user_id' => $user->id,
        ]);

        $topic = Topic::create([
            'ar_name' => 'الموضوع',
            'en_name' => 'Topic',
        ]);

        $meeting = Meeting::create([
            'group_id' => $group->id,
            'day_id' => $day->id,
            'topic_id' => $topic->id,
            'start_time' => '18:00:00',
            'end_time' => '19:30:00',
            'type' => 'open',
            'status' => 'available',
        ]);

        $response = $this->get(route('meetings.export.download', [
            'export_type' => 'cities',
            'cities' => [$city->id],
            'fields' => ['topic', 'time'],
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_wizard_filters_out_cities_with_no_meetings_or_virtual_meetings()
    {
        $day = Day::first();

        // City A: Has active non-virtual meetings
        $cityA = City::create(['ar_name' => 'مدينة أ', 'en_name' => 'City A']);
        $neighA = Neighborhood::create(['ar_name' => 'حي أ', 'en_name' => 'Neigh A', 'city_id' => $cityA->id]);
        $sb = ServiceBody::create([
            'ar_name' => 'كيان', 'en_name' => 'SB', 'day_id' => $day->id, 'date' => '2026-06-01',
            'start_time' => '10:00:00', 'end_time' => '12:00:00', 'location' => 'Loc'
        ]);
        $groupA = Group::create([
            'ar_name' => 'م أ', 'en_name' => 'G A', 'ar_gsr_name' => 'GSR', 'en_gsr_name' => 'GSR',
            'email' => 'a@example.com', 'phone' => '123', 'location' => 'Loc', 'ar_address' => 'Addr', 'en_address' => 'Addr',
            'group_type' => 'in-person', 'service_body_id' => $sb->id, 'neighborhood_id' => $neighA->id, 'user_id' => User::factory()->create()->id
        ]);
        $topic = Topic::create(['ar_name' => 'موضوع', 'en_name' => 'Topic']);
        Meeting::create([
            'group_id' => $groupA->id, 'day_id' => $day->id, 'topic_id' => $topic->id,
            'start_time' => '10:00:00', 'end_time' => '11:00:00', 'type' => 'open', 'status' => 'available'
        ]);

        // City B: Has only virtual/online meetings
        $cityB = City::create(['ar_name' => 'مدينة ب', 'en_name' => 'City B']);
        $neighB = Neighborhood::create(['ar_name' => 'حي ب', 'en_name' => 'Neigh B', 'city_id' => $cityB->id]);
        $groupB = Group::create([
            'ar_name' => 'م ب', 'en_name' => 'G B', 'ar_gsr_name' => 'GSR', 'en_gsr_name' => 'GSR',
            'email' => 'b@example.com', 'phone' => '123', 'location' => 'Loc', 'ar_address' => 'Addr', 'en_address' => 'Addr',
            'group_type' => 'online', 'service_body_id' => $sb->id, 'neighborhood_id' => $neighB->id, 'user_id' => User::factory()->create()->id
        ]);
        Meeting::create([
            'group_id' => $groupB->id, 'day_id' => $day->id, 'topic_id' => $topic->id,
            'start_time' => '10:00:00', 'end_time' => '11:00:00', 'type' => 'open', 'status' => 'available'
        ]);

        // City C: Has no meetings at all
        $cityC = City::create(['ar_name' => 'مدينة ج', 'en_name' => 'City C']);

        // Test Livewire component properties
        Livewire::test(MeetingExportWizard::class)
            ->assertSet('cities', function($cities) use ($cityA, $cityB, $cityC) {
                $ids = collect($cities)->pluck('id')->all();
                return in_array($cityA->id, $ids) && !in_array($cityB->id, $ids) && !in_array($cityC->id, $ids);
            });
    }

    public function test_pdf_export_merges_recurring_meetings_with_asterisk_and_footnote()
    {
        $thursday = Day::where('en_name', 'Thursday')->first() ?? Day::find(6);
        $friday = Day::where('en_name', 'Friday')->first() ?? Day::find(7);

        $city = City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo']);
        $neighborhood = Neighborhood::create([
            'ar_name' => 'مصر الجديدة',
            'en_name' => 'Heliopolis',
            'city_id' => $city->id,
        ]);
        $sb = ServiceBody::create([
            'ar_name' => 'منطقة مصر الجديدة', 'en_name' => 'Heliopolis Area', 'day_id' => $thursday->id,
            'start_time' => '18:00', 'end_time' => '19:00', 'location' => 'Cairo'
        ]);

        $user = User::factory()->create();

        $groupRoxy = Group::create([
            'ar_name' => 'روكسي', 'en_name' => 'Roxy', 'ar_gsr_name' => 'GSR', 'en_gsr_name' => 'GSR',
            'phone' => '123', 'location' => 'Loc', 'ar_address' => 'Addr', 'en_address' => 'Addr',
            'group_type' => 'فعلي', 'service_body_id' => $sb->id, 'neighborhood_id' => $neighborhood->id, 'user_id' => $user->id
        ]);

        $groupIsmailia = Group::create([
            'ar_name' => 'الحرية', 'en_name' => 'Al Horia', 'ar_gsr_name' => 'GSR', 'en_gsr_name' => 'GSR',
            'phone' => '456', 'location' => 'Loc', 'ar_address' => 'Addr', 'en_address' => 'Addr',
            'group_type' => 'فعلي', 'service_body_id' => $sb->id, 'neighborhood_id' => $neighborhood->id, 'user_id' => $user->id
        ]);

        $topicVaries = Topic::create(['ar_name' => 'متفرقة', 'en_name' => 'Varies']);
        $topicSpeaker = Topic::create(['ar_name' => 'متحدث', 'en_name' => 'Speaker']);

        // Roxy weekly meeting on Thursday
        Meeting::create([
            'group_id' => $groupRoxy->id, 'day_id' => $thursday->id, 'topic_id' => $topicVaries->id,
            'start_time' => '19:30:00', 'end_time' => '20:30:00', 'type' => 'closed', 'status' => 'available', 'recurrence' => ['weekly']
        ]);

        // Roxy 4th week recurring meeting on Thursday
        Meeting::create([
            'group_id' => $groupRoxy->id, 'day_id' => $thursday->id, 'topic_id' => $topicSpeaker->id,
            'start_time' => '19:30:00', 'end_time' => '20:30:00', 'type' => 'open', 'status' => 'available', 'recurrence' => ['4th']
        ]);

        // Standalone monthly meeting on Friday (no weekly meeting for Ismailia on Friday)
        Meeting::create([
            'group_id' => $groupIsmailia->id, 'day_id' => $friday->id, 'topic_id' => $topicVaries->id,
            'start_time' => '20:00:00', 'end_time' => '21:00:00', 'type' => 'open', 'status' => 'available', 'recurrence' => ['3rd']
        ]);

        // 1. Test pocket export HTML view
        $filterService = app(\App\Services\MeetingFilterService::class);
        $allMeetings = Meeting::whereIn('group_id', [$groupRoxy->id, $groupIsmailia->id])
            ->where('status', 'available')
            ->with(['group', 'day', 'topic', 'topics'])
            ->get();

        $merged = $filterService->mergeRecurringMeetings($allMeetings, []);
        $thuMeetings = $merged->filter(fn($m) => $m->day_id === $thursday->id);
        $friMeetings = $merged->filter(fn($m) => $m->day_id === $friday->id);

        // Roxy is merged into 1 card on Thursday with variation
        $this->assertCount(1, $thuMeetings);
        $roxyMeeting = $thuMeetings->first();
        $this->assertEquals($groupRoxy->id, $roxyMeeting->group_id);
        $this->assertCount(1, $roxyMeeting->recurring_variations);

        // Standalone on Friday has no variation and retains its 3rd recurrence
        $this->assertCount(1, $friMeetings);
        $this->assertCount(0, $friMeetings->first()->recurring_variations);

        // Render the day table partial for Thursday
        $htmlThu = view('exports.meetings-day-table-partial', [
            'dayName' => 'الخميس',
            'meetings' => $thuMeetings,
            'fields' => ['topic', 'time', 'type'],
            'pageSize' => 'A4',
        ])->render();

        $this->assertStringContainsString('روكسي', $htmlThu);
        $this->assertStringContainsString('*', $htmlThu);
        $this->assertStringContainsString('day-footnotes', $htmlThu);
        $this->assertStringContainsString('الأسبوع الرابع', $htmlThu);
        $this->assertStringContainsString('مفتوح', $htmlThu);

        // Render the day table partial for Friday
        $htmlFri = view('exports.meetings-day-table-partial', [
            'dayName' => 'الجمعة',
            'meetings' => $friMeetings,
            'fields' => ['topic', 'time', 'type'],
            'pageSize' => 'A4',
        ])->render();

        $this->assertStringContainsString('الحرية', $htmlFri);
        $this->assertStringContainsString('الأسبوع الثالث', $htmlFri);
        $this->assertStringNotContainsString('day-footnotes', $htmlFri);

        // 2. Test direct PDF view
        $htmlDirect = view('pdf.meetings', [
            'meetings' => $merged,
        ])->render();

        $this->assertStringContainsString('روكسي', $htmlDirect);
        $this->assertStringContainsString('* روكسي: الأسبوع الرابع', $htmlDirect);
        $this->assertStringContainsString('الحرية', $htmlDirect);
        $this->assertStringContainsString('الأسبوع الثالث', $htmlDirect);
    }
}
