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
        $topic = Topic::forceCreate(['id' => 1, 'en_name' => 'Discussion', 'ar_name' => 'مناقشة']);
        $city = \App\Models\City::forceCreate(['id' => 2, 'en_name' => 'Giza', 'ar_name' => 'الجيزة']);
        $serviceBody = ServiceBody::forceCreate([
            'id' => 2, 'en_name' => 'Giza', 'ar_name' => 'الجيزة', 'day_id' => 6,
            'start_time' => '18:00', 'end_time' => '19:00', 'location' => 'Giza'
        ]);
        $neighborhood = Neighborhood::forceCreate(['id' => 2, 'en_name' => 'Dokki', 'ar_name' => 'الدقي', 'city_id' => 2]);
        $user = \App\Models\User::factory()->create();
        $group = Group::forceCreate([
            'id' => 186, 'en_name' => 'Dokki Group', 'ar_name' => 'مجموعة الدقي', 'group_type' => 'فعلي',
            'ar_gsr_name' => 'Test', 'en_gsr_name' => 'Test', 'phone' => '1234567890', 'location' => '',
            'service_body_id' => 2, 'neighborhood_id' => 2, 'user_id' => $user->id,
            'ar_address' => 'الدقي', 'en_address' => 'Dokki'
        ]);

        // Create a weekly meeting (no recurrence array or contains 'weekly')
        $weeklyMeeting = Meeting::forceCreate([
            'id' => 320, 'day_id' => 6, 'group_id' => 186, 'topic_id' => 1, 'start_time' => '20:45', 'end_time' => '21:45',
            'type' => 'open', 'lang' => 'arabic', 'status' => 'available', 'recurrence' => null
        ]);

        // Create a monthly recurring meeting (recurrence has '1st')
        $monthlyMeeting = Meeting::forceCreate([
            'id' => 321, 'day_id' => 6, 'group_id' => 186, 'topic_id' => 1, 'start_time' => '21:00', 'end_time' => '22:00',
            'type' => 'open', 'lang' => 'arabic', 'status' => 'available', 'recurrence' => ['1st']
        ]);

        // 1. Default (recurrence is 'weekly') -> should return both weekly and monthly recurring meetings
        $component = Livewire::test(MeetingFilter::class)
            ->set('day', 'الخميس');
        $meetings = $component->viewData('meetings');
        $this->assertTrue($meetings->contains('id', 320));
        $this->assertTrue($meetings->contains('id', 321));

        // 2. Filter by 'monthly' -> should return only the monthly meeting
        $component->set('recurrence', 'monthly');
        $meetings = $component->viewData('meetings');
        $this->assertFalse($meetings->contains('id', 320));
        $this->assertTrue($meetings->contains('id', 321));

        // 3. Filter by '1st' -> should return only the monthly meeting
        $component->set('recurrence', '1st');
        $meetings = $component->viewData('meetings');
        $this->assertFalse($meetings->contains('id', 320));
        $this->assertTrue($meetings->contains('id', 321));
    }
}
