<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Day;
use App\Models\DirectOnlineGroup;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ArabicSearchTest extends TestCase
{
    use RefreshDatabase;

    private Day $day;

    private Topic $topic;

    private Group $alexGroup;

    private DirectOnlineGroup $onlineGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->day = Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $this->topic = Topic::create(['ar_name' => 'مفتوح', 'en_name' => 'Open']);

        $alexandria = City::create([
            'ar_name' => 'إسكندرية',
            'en_name' => 'Alexandria',
            'user_id' => $user->id,
        ]);

        $smouha = Neighborhood::create([
            'city_id' => $alexandria->id,
            'ar_name' => 'سموحة',
            'en_name' => 'Smouha',
            'user_id' => $user->id,
        ]);

        $serviceBody = ServiceBody::create([
            'ar_name' => 'لجنة الإسكندرية',
            'en_name' => 'Alexandria Area',
            'day_id' => $this->day->id,
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
            'location' => 'Alexandria',
        ]);

        $this->alexGroup = Group::create([
            'ar_name' => 'مَجْمُوعَةُ الأَمَلِ ١٢٣',
            'en_name' => 'Hope 123 Group',
            'ar_gsr_name' => 'أحمد',
            'en_gsr_name' => 'Ahmed',
            'phone' => '+201000000000',
            'location' => 'https://maps.google.com/?q=31.2,29.9',
            'ar_address' => 'شارع مصطفى كامل، إسكندرية',
            'en_address' => 'Mostafa Kamel St, Alexandria',
            'service_body_id' => $serviceBody->id,
            'neighborhood_id' => $smouha->id,
            'user_id' => $user->id,
            'group_type' => 'in_person',
        ]);

        Meeting::create([
            'group_id' => $this->alexGroup->id,
            'day_id' => $this->day->id,
            'topic_id' => $this->topic->id,
            'start_time' => '19:00',
            'end_time' => '20:00',
            'type' => 'in_person',
            'lang' => 'arabic',
            'status' => 'available',
        ]);

        $this->onlineGroup = DirectOnlineGroup::create([
            'ar_name' => 'مجموعة السَّلامِ أونلاين',
            'en_name' => 'Salam Online',
            'location' => 'https://zoom.us/j/123456',
            'user_id' => $user->id,
        ]);

        Meeting::create([
            'direct_online_group_id' => $this->onlineGroup->id,
            'day_id' => $this->day->id,
            'topic_id' => $this->topic->id,
            'start_time' => '20:00',
            'end_time' => '21:00',
            'type' => 'online',
            'lang' => 'arabic',
            'status' => 'available',
        ]);
    }

    public function test_search_matches_alef_hamza_ta_marbuta_alif_maqsura_tashkeel_and_digits(): void
    {
        // Alef/hamza variant: اسكندرية matches إسكندرية
        $this->getJson('/api/v1/meetings?search=اسكندرية')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Ta marbuta (ه vs ة) + alef/tashkeel: مجموعه الامل matches مَجْمُوعَةُ الأَمَلِ
        $this->getJson('/api/v1/meetings?search='.urlencode('مجموعه الامل'))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Alif maqsura (ي vs ى): مصطفي matches مصطفى
        $this->getJson('/api/v1/meetings?search='.urlencode('مصطفي'))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Arabic-Indic digits vs Western digits: 123 matches ١٢٣
        $this->getJson('/api/v1/meetings?search=123')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Direct online group with tashkeel: السلام matches السَّلامِ
        $this->getJson('/api/v1/meetings?virtualOnly=1&search='.urlencode('السلام'))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_search_reindex_command_backfills_null_search_text(): void
    {
        DB::table('groups')->update(['search_text' => null]);
        DB::table('direct_online_groups')->update(['search_text' => null]);
        DB::table('cities')->update(['search_text' => null]);
        DB::table('neighborhoods')->update(['search_text' => null]);

        $this->artisan('search:reindex')
            ->assertExitCode(0);

        $this->assertNotNull($this->alexGroup->fresh()->search_text);
        $this->assertNotNull($this->onlineGroup->fresh()->search_text);
        $this->assertStringContainsString('اسكندريه', $this->alexGroup->fresh()->search_text);
        $this->assertStringContainsString('123', $this->alexGroup->fresh()->search_text);
    }
}
