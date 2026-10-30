<?php

namespace Tests\Feature\Livewire;

use App\Livewire\MeetingFilter;
use App\Models\Meeting;
use App\Models\Topic;
use App\Models\Day;
use App\Models\Group;
use App\Models\ServiceBody;
use App\Models\Neighborhood;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MeetingFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_meetings_only_clears_day_and_returns_meetings()
    {
        // 1. Setup days (pre-seeded in migration)
        $thursday = Day::find(6);
        $saturday = Day::find(1);

        // 2. Setup topic
        $topic = Topic::forceCreate(['id' => 27, 'en_name' => 'Group Business Meeting', 'ar_name' => 'اجتماع عمل المجموعة']);

        // 3. Setup parent records
        $city = \App\Models\City::forceCreate([
            'id' => 1,
            'en_name' => 'Cairo',
            'ar_name' => 'القاهرة'
        ]);

        $serviceBody = ServiceBody::forceCreate([
            'id' => 1,
            'en_name' => 'Cairo',
            'ar_name' => 'القاهرة',
            'day_id' => 6,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'location' => 'Cairo'
        ]);

        $neighborhood = Neighborhood::forceCreate([
            'id' => 1,
            'en_name' => 'Heliopolis',
            'ar_name' => 'مصر الجديدة',
            'city_id' => 1
        ]);

        $user = \App\Models\User::factory()->create();

        // 4. Setup group
        $group = Group::forceCreate([
            'id' => 185,
            'en_name' => 'Roxy',
            'ar_name' => 'روكسي',
            'group_type' => 'فعلي',
            'ar_gsr_name' => 'Test',
            'en_gsr_name' => 'Test',
            'phone' => '1234567890',
            'location' => '',
            'service_body_id' => 1,
            'neighborhood_id' => 1,
            'user_id' => $user->id,
            'ar_address' => 'روكسي',
            'en_address' => 'Roxy'
        ]);

        // 5. Setup meeting
        $meeting = Meeting::forceCreate([
            'id' => 319,
            'day_id' => 6,
            'group_id' => 185,
            'topic_id' => 27,
            'start_time' => '20:45',
            'end_time' => '21:45',
            'type' => 'closed',
            'lang' => 'arabic',
            'status' => 'available',
            'recurrence' => ['3rd']
        ]);
        $meeting->topics()->attach($topic->id);

        Livewire::test(MeetingFilter::class)
            ->set('day', 'Thursday')
            ->set('businessMeetingsOnly', false)
            ->call('toggleBusinessMeetingsOnly')
            ->assertSet('businessMeetingsOnly', true)
            ->assertSet('day', '')
            ->assertSet('virtualOnly', false)
            ->assertSet('englishOnly', false);
    }

    public function test_recurrence_filtering_behavior()
    {
        $thursday = Day::find(6);
        $friday = Day::find(7);
        $topic = Topic::forceCreate(['id' => 1, 'en_name' => 'Discussion', 'ar_name' => 'مناقشة']);
        $speakerTopic = Topic::forceCreate(['id' => 2, 'en_name' => 'Speaker', 'ar_name' => 'متحدث']);
        $city = \App\Models\City::forceCreate(['id' => 2, 'en_name' => 'Giza', 'ar_name' => 'الجيزة']);
        $serviceBody = ServiceBody::forceCreate([
            'id' => 2, 'en_name' => 'Giza', 'ar_name' => 'الجيزة', 'day_id' => 6,
            'start_time' => '18:00', 'end_time' => '19:00', 'location' => 'Giza'
        ]);
        $neighborhood = Neighborhood::forceCreate(['id' => 2, 'en_name' => 'Dokki', 'ar_name' => 'الدقي', 'city_id' => 2]);
        $user = \App\Models\User::factory()->create();
        $group = Group::forceCreate([
            'id' => 186, 'en_name' => 'Dokki Group', 'ar_name' => 'مجموعة الدقي', 'group_type' => 'فعلي',
            'ar_gsr_name' => 'Test', 'en_gsr_name' => 'Test', 'phone' => '1234567890', 'location' => 'https://maps.google.com',
            'service_body_id' => 2, 'neighborhood_id' => 2, 'user_id' => $user->id,
            'ar_address' => 'الدقي', 'en_address' => 'Dokki'
        ]);
        $weeklyOnlyGroup = Group::forceCreate([
            'id' => 187, 'en_name' => 'Agouza Group', 'ar_name' => 'مجموعة العجوزة', 'group_type' => 'فعلي',
            'ar_gsr_name' => 'Test', 'en_gsr_name' => 'Test', 'phone' => '1234567890', 'location' => '',
            'service_body_id' => 2, 'neighborhood_id' => 2, 'user_id' => $user->id,
            'ar_address' => 'العجوزة', 'en_address' => 'Agouza'
        ]);

        // Create a weekly closed meeting for Dokki Group on Thursday
        $weeklyMeeting = Meeting::forceCreate([
            'id' => 320, 'day_id' => 6, 'group_id' => 186, 'topic_id' => 1, 'start_time' => '20:45', 'end_time' => '21:45',
            'type' => 'closed', 'lang' => 'arabic', 'status' => 'available', 'recurrence' => null
        ]);
        $weeklyMeeting->topics()->attach($topic->id);

        // Create a monthly recurring open meeting on the same day for the same group (recurrence has '1st')
        $monthlyMeeting = Meeting::forceCreate([
            'id' => 321, 'day_id' => 6, 'group_id' => 186, 'topic_id' => 2, 'start_time' => '21:00', 'end_time' => '22:00',
            'type' => 'open', 'lang' => 'arabic', 'status' => 'available', 'recurrence' => ['1st']
        ]);
        $monthlyMeeting->topics()->attach($speakerTopic->id);

        // Create a weekly-only closed meeting for Agouza Group on Thursday
        Meeting::forceCreate([
            'id' => 322, 'day_id' => 6, 'group_id' => 187, 'topic_id' => 1, 'start_time' => '19:00', 'end_time' => '20:00',
            'type' => 'closed', 'lang' => 'arabic', 'status' => 'available', 'recurrence' => ['weekly']
        ]);

        // Create a standalone monthly meeting on Friday (no weekly sibling on Friday)
        Meeting::forceCreate([
            'id' => 323, 'day_id' => 7, 'group_id' => 187, 'topic_id' => 1, 'start_time' => '20:00', 'end_time' => '21:00',
            'type' => 'open', 'lang' => 'arabic', 'status' => 'available', 'recurrence' => ['3rd']
        ]);

        // 1. Default ('weekly') on Thursday -> merges 321 into 320's recurring_variations (no separate card for 321)
        $component = Livewire::test(MeetingFilter::class)
            ->set('day', 'الخميس');
        $meetings = $component->viewData('meetings');
        $this->assertTrue($meetings->contains('id', 320));
        $this->assertFalse($meetings->contains('id', 321));
        $this->assertTrue($meetings->contains('id', 322));
        $mergedParent = $meetings->firstWhere('id', 320);
        $this->assertCount(1, $mergedParent->recurring_variations);
        $this->assertEquals(321, $mergedParent->recurring_variations->first()->id);
        $component->assertSeeHtml('meeting-recurring-subrow');

        // 2. Filter by 'monthly' -> returns merged parent card 320 (since its variation 321 is monthly) and excludes weekly-only 322
        $component->set('recurrence', 'monthly');
        $meetings = $component->viewData('meetings');
        $this->assertTrue($meetings->contains('id', 320));
        $this->assertFalse($meetings->contains('id', 321));
        $this->assertFalse($meetings->contains('id', 322));
        $this->assertTrue($meetings->firstWhere('id', 320)->recurring_variations->first()->is_filter_match);

        // 3. Filter by '1st' -> returns merged parent card 320 with filter-matched variation 321
        $component->set('recurrence', '1st');
        $meetings = $component->viewData('meetings');
        $this->assertTrue($meetings->contains('id', 320));
        $this->assertFalse($meetings->contains('id', 322));

        // 4. Filter by type 'open' with default 'weekly' recurrence -> returns merged parent card 320 because embedded 321 is open
        $component->set('recurrence', 'weekly')->set('type', 'open');
        $meetings = $component->viewData('meetings');
        $this->assertTrue($meetings->contains('id', 320));
        $this->assertFalse($meetings->contains('id', 322));

        // 5. Standalone monthly meeting on Friday (no weekly meeting on that day) remains its own standalone card
        $component->set('type', '')->set('day', 'الجمعة');
        $meetings = $component->viewData('meetings');
        $this->assertTrue($meetings->contains('id', 323));
    }

    public function test_virtual_meetings_filter_displays_hint()
    {
        app()->setLocale('ar');

        // Initially virtualOnly is false, hint should not be rendered
        Livewire::test(MeetingFilter::class)
            ->assertDontSee('المجموعات الافتراضية لا تتبع الخدمات المحلية')
            ->call('toggleVirtualOnly')
            ->assertSet('virtualOnly', true)
            ->assertSee('المجموعات الافتراضية لا تتبع الخدمات المحلية')
            ->assertSee(__('messages.virtual_meetings_hint_title'));

        // Test in English
        app()->setLocale('en');

        Livewire::test(MeetingFilter::class)
            ->set('virtualOnly', true)
            ->assertSee('Virtual groups are not directly affiliated')
            ->assertSee(__('messages.virtual_meetings_hint_title'));
    }

    public function test_gsr_contact_hidden_for_guests_with_gesture_and_visible_for_logged_in_users()
    {
        app()->setLocale('en');

        // Setup parent and meeting with a group that has GSR phone
        $city = \App\Models\City::forceCreate(['id' => 10, 'en_name' => 'Giza', 'ar_name' => 'الجيزة']);
        $serviceBody = ServiceBody::forceCreate([
            'id' => 10, 'en_name' => 'Giza Area', 'ar_name' => 'منطقة الجيزة',
            'day_id' => 1, 'start_time' => '18:00', 'end_time' => '19:00', 'location' => 'Giza'
        ]);
        $neighborhood = Neighborhood::forceCreate([
            'id' => 10, 'en_name' => 'Dokki', 'ar_name' => 'الدقي', 'city_id' => 10
        ]);
        $user = \App\Models\User::factory()->create();

        $group = Group::forceCreate([
            'id' => 250,
            'en_name' => 'Hope Group',
            'ar_name' => 'مجموعة الأمل',
            'group_type' => 'فعلي',
            'neighborhood_id' => 10,
            'service_body_id' => 10,
            'user_id' => $user->id,
            'phone' => '+201000000000',
            'en_gsr_name' => 'John Doe',
            'ar_gsr_name' => 'جون دو',
            'ar_address' => 'الدقي',
            'en_address' => 'Dokki',
            'location' => 'https://maps.google.com'
        ]);

        $topic = Topic::firstOrCreate(['id' => 1], ['en_name' => 'General Topic', 'ar_name' => 'موضوع عام']);

        $today = Day::where('en_name', now()->format('l'))->first() ?? Day::first();

        $meeting = Meeting::forceCreate([
            'id' => 450,
            'day_id' => $today->id,
            'group_id' => 250,
            'topic_id' => $topic->id,
            'start_time' => '19:00',
            'end_time' => '20:30',
            'type' => 'open',
            'lang' => 'english',
            'status' => 'available'
        ]);

        // 1. As a public guest (unauthenticated): GSR name & phone MUST NOT be in initial HTML
        $this->assertFalse(auth()->check());
        $responseGuest = Livewire::test(MeetingFilter::class);
        $responseGuest->assertSeeHtml('revealed: false');
        $responseGuest->assertSeeHtml('@click="handleNameClick()"');
        $responseGuest->assertSeeHtml('style="display: none;"');
        $responseGuest->assertDontSeeHtml('+201000000000');
        $responseGuest->assertDontSeeHtml('John Doe');
        $responseGuest->assertDontSeeHtml('جون دو');

        // 1b. Public search on /meetings must never match GSR name or phone number
        Livewire::test(MeetingFilter::class)
            ->set('search', 'John Doe')
            ->assertViewHas('meetings', fn ($m) => $m->isEmpty());
        Livewire::test(MeetingFilter::class)
            ->set('search', '+201000000000')
            ->assertViewHas('meetings', fn ($m) => $m->isEmpty());

        // 2. As an authenticated user: GSR name & phone are visible
        $this->actingAs($user);
        $this->assertTrue(auth()->check());
        $responseAuth = Livewire::test(MeetingFilter::class);
        $responseAuth->assertSeeHtml('revealed: true');
        $responseAuth->assertDontSeeHtml('style="display: none;"');
        $responseAuth->assertSeeHtml('+201000000000');
        $responseAuth->assertSeeHtml('John Doe');
    }
}
