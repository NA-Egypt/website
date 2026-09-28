<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RscStructureAndMeetingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $rscUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@example.com',
        ]);
        $this->superAdmin->assignRole('super admin');

        $this->rscUser = User::factory()->create([
            'email' => 'rsc.officer@naegypt.org',
        ]);
        $this->rscUser->assignRole('rsc');

        $this->regularUser = User::factory()->create([
            'email' => 'member@example.com',
        ]);
    }

    /**
     * RSC officers have read-only access to ServiceBody, Map, City, Neighborhood, Topic, and Meetings.
     */
    public function test_rsc_officer_can_access_read_only_structure_and_meetings(): void
    {
        $this->actingAs($this->rscUser);

        $this->get(route('serviceBody.index'))->assertStatus(200);
        $this->get(route('serviceBody.map'))->assertStatus(200);
        $this->get(route('city.index'))->assertStatus(200);
        $this->get(route('neighborhood.index'))->assertStatus(200);
        $this->get(route('topic.index'))->assertStatus(200);
        $this->get(route('meeting.index'))->assertStatus(200);
    }

    /**
     * RSC officers are forbidden from mutation routes (create, edit, transactions).
     */
    public function test_rsc_officer_cannot_mutate_structure_or_view_transactions(): void
    {
        $this->actingAs($this->rscUser);

        $this->get(route('serviceBody.create'))->assertStatus(403);
        $this->get(route('city.create'))->assertStatus(403);
        $this->get(route('neighborhood.create'))->assertStatus(403);
        $this->get(route('topic.create'))->assertStatus(403);
        $this->get(route('transactions.index'))->assertStatus(403);
    }

    /**
     * Regular users cannot access structure or meeting index.
     */
    public function test_regular_user_cannot_access_structure_or_meeting_index(): void
    {
        $this->actingAs($this->regularUser);

        $this->get(route('serviceBody.index'))->assertStatus(403);
        $this->get(route('serviceBody.map'))->assertStatus(403);
        $this->get(route('city.index'))->assertStatus(403);
        $this->get(route('neighborhood.index'))->assertStatus(403);
        $this->get(route('topic.index'))->assertStatus(403);
        $this->get(route('meeting.index'))->assertStatus(403);
    }
}
