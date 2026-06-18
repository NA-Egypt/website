<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\Topic;
use App\Models\Day;
use App\Models\Meeting;
use App\Models\ServiceBody;
use App\Models\City;
use App\Models\Neighborhood;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingTopicExclusivityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
    }

    public function test_group_business_meeting_topic_cannot_be_saved_with_other_topics()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super admin');
        $this->actingAs($admin);

        // Seed required values
        $day = Day::firstOrCreate(['id' => 1], ['name' => 'Saturday']);
        
        $serviceBody = ServiceBody::firstOrCreate(['id' => 1], [
            'en_name' => 'Cairo',
            'ar_name' => 'القاهرة',
            'day_id' => $day->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'location' => 'Cairo'
        ]);

        $city = City::firstOrCreate(['id' => 1], [
            'en_name' => 'Cairo',
            'ar_name' => 'القاهرة'
        ]);

        $neighborhood = Neighborhood::firstOrCreate(['id' => 1], [
            'en_name' => 'Heliopolis',
            'ar_name' => 'مصر الجديدة',
            'city_id' => $city->id
        ]);

        $group = Group::forceCreate([
            'id' => 1,
            'en_name' => 'Group A',
            'ar_name' => 'المجموعة أ',
            'group_type' => 'فعلي',
            'ar_gsr_name' => 'Test',
            'en_gsr_name' => 'Test',
            'phone' => '1234567890',
            'location' => 'Cairo',
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
            'user_id' => $admin->id,
            'ar_address' => 'العنوان',
            'en_address' => 'Address'
        ]);

        $businessTopic = Topic::forceCreate([
            'id' => 27,
            'en_name' => 'Group Business Meeting',
            'ar_name' => 'اجتماع عمل المجموعة'
        ]);

        $otherTopic = Topic::forceCreate([
            'id' => 6,
            'en_name' => 'Speaker',
            'ar_name' => 'متحدث'
        ]);

        // Submit both topics
        $response = $this->post(route('meeting.store'), [
            'group_id' => $group->id,
            'day_id' => $day->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'type' => 'open',
            'lang' => 'english',
            'status' => 'available',
            'recurrence' => ['weekly'],
            'topics' => [$businessTopic->id, $otherTopic->id]
        ]);

        $response->assertSessionHasErrors('topics');
    }

    public function test_group_business_meeting_topic_cannot_be_updated_with_other_topics()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super admin');
        $this->actingAs($admin);

        // Seed required values
        $day = Day::firstOrCreate(['id' => 1], ['name' => 'Saturday']);

        $serviceBody = ServiceBody::firstOrCreate(['id' => 1], [
            'en_name' => 'Cairo',
            'ar_name' => 'القاهرة',
            'day_id' => $day->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'location' => 'Cairo'
        ]);

        $city = City::firstOrCreate(['id' => 1], [
            'en_name' => 'Cairo',
            'ar_name' => 'القاهرة'
        ]);

        $neighborhood = Neighborhood::firstOrCreate(['id' => 1], [
            'en_name' => 'Heliopolis',
            'ar_name' => 'مصر الجديدة',
            'city_id' => $city->id
        ]);

        $group = Group::forceCreate([
            'id' => 1,
            'en_name' => 'Group A',
            'ar_name' => 'المجموعة أ',
            'group_type' => 'فعلي',
            'ar_gsr_name' => 'Test',
            'en_gsr_name' => 'Test',
            'phone' => '1234567890',
            'location' => 'Cairo',
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $neighborhood->id,
            'user_id' => $admin->id,
            'ar_address' => 'العنوان',
            'en_address' => 'Address'
        ]);

        $businessTopic = Topic::forceCreate([
            'id' => 27,
            'en_name' => 'Group Business Meeting',
            'ar_name' => 'اجتماع عمل المجموعة'
        ]);

        $otherTopic = Topic::forceCreate([
            'id' => 6,
            'en_name' => 'Speaker',
            'ar_name' => 'متحدث'
        ]);

        $meeting = Meeting::forceCreate([
            'id' => 1,
            'day_id' => $day->id,
            'group_id' => $group->id,
            'topic_id' => $otherTopic->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'type' => 'open',
            'lang' => 'english',
            'status' => 'available',
            'recurrence' => ['weekly']
        ]);

        // Submit both topics
        $response = $this->put(route('meeting.update', $meeting->id), [
            'group_id' => $group->id,
            'day_id' => $day->id,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'type' => 'open',
            'lang' => 'english',
            'status' => 'available',
            'recurrence' => ['weekly'],
            'topics' => [$businessTopic->id, $otherTopic->id]
        ]);

        $response->assertSessionHasErrors('topics');
    }
}
