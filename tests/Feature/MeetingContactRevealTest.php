<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Meeting;
use App\Models\Group;
use App\Models\DirectOnlineGroup;
use App\Models\Day;
use App\Models\Topic;
use App\Models\User;
use App\Models\City;
use App\Models\ServiceBody;
use App\Models\Neighborhood;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MeetingContactRevealTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $topic;
    protected $day;
    protected $serviceBody;
    protected $neighborhood;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::forceCreate([
            'id' => 101,
            'name' => 'testuser',
            'email' => 'test@naegypt.org',
            'display_name' => 'Test User',
        ]);
        $this->topic = Topic::forceCreate([
            'id' => 101,
            'en_name' => 'Test Topic',
            'ar_name' => 'موضوع تجريبي',
        ]);
        $this->day = Day::first();

        $city = City::forceCreate([
            'id' => 1,
            'en_name' => 'Cairo',
            'ar_name' => 'القاهرة'
        ]);

        $this->serviceBody = ServiceBody::forceCreate([
            'id' => 1,
            'en_name' => 'Cairo',
            'ar_name' => 'القاهرة',
            'day_id' => $this->day->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'location' => 'Cairo'
        ]);

        $this->neighborhood = Neighborhood::forceCreate([
            'id' => 1,
            'en_name' => 'Heliopolis',
            'ar_name' => 'مصر الجديدة',
            'city_id' => $city->id
        ]);
    }

    public function test_reveal_meeting_contact_for_in_person_group()
    {
        $group = Group::forceCreate([
            'id' => 8881,
            'en_name' => 'Test In-Person Group',
            'ar_name' => 'مجموعة حضورية تجريبية',
            'group_type' => 'فعلي',
            'ar_gsr_name' => 'علي أحمد',
            'en_gsr_name' => 'Ali Ahmed',
            'phone' => '01011112222',
            'location' => 'https://maps.google.com',
            'ar_address' => 'عنوان عربي',
            'en_address' => 'English Address',
            'service_body_id' => $this->serviceBody->id,
            'neighborhood_id' => $this->neighborhood->id,
            'user_id' => $this->user->id,
        ]);

        $meeting = Meeting::forceCreate([
            'id' => 8881,
            'day_id' => $this->day->id,
            'group_id' => $group->id,
            'topic_id' => $this->topic->id,
            'start_time' => '19:00',
            'end_time' => '20:30',
            'type' => 'open',
            'status' => 'available',
        ]);

        $response = $this->postJson(route('meetings.contact', ['meeting' => $meeting->id]));

        $response->assertStatus(200)
            ->assertJson([
                'phone' => '01011112222',
                'ar_gsr_name' => 'علي أحمد',
                'en_gsr_name' => 'Ali Ahmed',
            ]);
    }

    public function test_reveal_meeting_contact_for_direct_online_group()
    {
        $dog = DirectOnlineGroup::forceCreate([
            'id' => 8882,
            'en_name' => 'Test Online Group',
            'ar_name' => 'مجموعة أونلاين تجريبية',
            'ar_gsr_name' => 'محمود كمال',
            'en_gsr_name' => 'Mahmoud Kamal',
            'phone' => '01033334444',
            'location' => 'https://zoom.us/j/123456789',
            'user_id' => $this->user->id,
        ]);

        $meeting = Meeting::forceCreate([
            'id' => 8882,
            'day_id' => $this->day->id,
            'direct_online_group_id' => $dog->id,
            'topic_id' => $this->topic->id,
            'start_time' => '20:00',
            'end_time' => '21:30',
            'type' => 'open',
            'status' => 'available',
        ]);

        $response = $this->postJson(route('meetings.contact', ['meeting' => $meeting->id]));

        $response->assertStatus(200)
            ->assertJson([
                'phone' => '01033334444',
                'ar_gsr_name' => 'محمود كمال',
                'en_gsr_name' => 'Mahmoud Kamal',
            ]);
    }

    public function test_reveal_meeting_contact_blocks_bots()
    {
        $group = Group::forceCreate([
            'id' => 8883,
            'en_name' => 'Bot Test Group',
            'ar_name' => 'مجموعة فحص البوت',
            'group_type' => 'فعلي',
            'ar_gsr_name' => 'بوت تجريبي',
            'en_gsr_name' => 'Bot Test',
            'phone' => '01055556666',
            'ar_address' => 'عنوان عربي',
            'en_address' => 'English Address',
            'service_body_id' => $this->serviceBody->id,
            'neighborhood_id' => $this->neighborhood->id,
            'user_id' => $this->user->id,
        ]);

        $meeting = Meeting::forceCreate([
            'id' => 8883,
            'day_id' => $this->day->id,
            'group_id' => $group->id,
            'topic_id' => $this->topic->id,
            'start_time' => '19:00',
            'end_time' => '20:30',
            'type' => 'open',
            'status' => 'available',
        ]);

        $response = $this->withHeaders(['User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)'])
            ->postJson(route('meetings.contact', ['meeting' => $meeting->id]));

        $response->assertStatus(403);
    }

    public function test_backward_compatibility_group_contact_route()
    {
        $group = Group::forceCreate([
            'id' => 8884,
            'en_name' => 'Legacy Group',
            'ar_name' => 'مجموعة قديمة',
            'group_type' => 'فعلي',
            'ar_gsr_name' => 'سامح نبيل',
            'en_gsr_name' => 'Sameh Nabil',
            'phone' => '01077778888',
            'ar_address' => 'عنوان عربي',
            'en_address' => 'English Address',
            'service_body_id' => $this->serviceBody->id,
            'neighborhood_id' => $this->neighborhood->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->postJson(route('meetings.group.contact', ['group' => $group->id]));

        $response->assertStatus(200)
            ->assertJson([
                'phone' => '01077778888',
                'ar_gsr_name' => 'سامح نبيل',
                'en_gsr_name' => 'Sameh Nabil',
            ]);
    }
}
