<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceCommittee;
use App\Models\CommitteeReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommitteeReportTableTest extends TestCase
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
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);
    }

    protected function createCommittee($user)
    {
        return ServiceCommittee::create([
            'ar_name' => 'اللجنة الفرعية',
            'en_name' => 'Sub Committee',
            'email' => $user->email,
            'user_id' => $user->id,
            'ar_address' => 'عنوان',
            'en_address' => 'Address',
            'location' => 'https://maps.google.com',
            'chairman_name' => 'Chairman',
            'chairman_phone' => '12345678',
        ]);
    }

    public function test_committee_report_can_be_saved_with_table_html()
    {
        $user = User::factory()->create(['email' => 'committee@naegypt.org']);
        $committee = $this->createCommittee($user);

        $this->actingAs($user);

        $tableHtml = '<table><tr><td>Header 1</td><td>Header 2</td></tr><tr><td>Cell 1</td><td>Cell 2</td></tr></table>';

        $response = $this->post(route('committee-reports.store'), [
            'meeting_date' => '2026-06-16',
            'meeting_day_description' => 'Tuesday',
            'sections' => [
                ['headline' => 'Table Section', 'content' => $tableHtml]
            ],
            'positions' => [
                ['name' => 'Chairman', 'status' => 'Present', 'election' => '0', 'member_name' => 'Ahmed Ali'],
                ['name' => 'Treasurer', 'status' => 'Absent', 'election' => '1', 'member_name' => 'Hassan']
            ],
            'status' => 'draft',
            'is_exceptional' => '0',
            'attended_members' => 'Member A, Member B',
        ]);

        $response->assertRedirect(route('committee-reports.index'));

        // Retrieve and check DB
        $report = CommitteeReport::where('service_committee_id', $committee->id)->first();
        $this->assertNotNull($report);
        
        $bodySections = $report->body_sections;
        $this->assertCount(1, $bodySections);
        $this->assertEquals('Table Section', $bodySections[0]['headline']);
        $this->assertStringContainsString('<table>', $bodySections[0]['content']);
        $this->assertStringContainsString('<td>Cell 1</td>', $bodySections[0]['content']);

        $positionsStatus = $report->positions_status;
        $this->assertCount(2, $positionsStatus);
        $this->assertEquals('Ahmed Ali', $positionsStatus[0]['member_name']);
        $this->assertEquals('Hassan', $positionsStatus[1]['member_name']);

        // Check if show page displays the table and positions correctly
        $showResponse = $this->get(route('committee-reports.show', $report->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('<table>', false);
        $showResponse->assertSee('<td>Cell 1</td>', false);
        $showResponse->assertSee('Ahmed Ali');
        $showResponse->assertSee('Hassan');

        // Check if PDF rendering works and displays positions correctly
        $pdfResponse = $this->get(route('committee-reports.pdf', $report->id));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('Content-Type'));
    }
}
