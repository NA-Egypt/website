<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ChangeRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\ChangeRequestMail;
use Tests\TestCase;

class ChangeRequestTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
        
        Storage::fake('local');
        Mail::fake();
    }

    public function test_guest_cannot_access_change_requests()
    {
        $this->get(route('change-requests.index'))
            ->assertRedirect('/');

        $this->get(route('change-requests.create'))
            ->assertRedirect('/');

        $this->post(route('change-requests.store'), [])
            ->assertRedirect('/');
    }

    public function test_committee_user_can_submit_change_request()
    {
        $user = User::factory()->create();
        $user->assignRole('Committees');

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->post(route('change-requests.store'), [
            'request_type' => 'meetings_groups',
            'subject' => 'Please add a new meeting',
            'description' => 'Meeting details: Monday 8 PM in Cairo.',
            'attachment' => $file,
        ]);

        $response->assertRedirect(route('change-requests.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('change_requests', [
            'user_id' => $user->id,
            'request_type' => 'meetings_groups',
            'subject' => 'Please add a new meeting',
            'description' => 'Meeting details: Monday 8 PM in Cairo.',
            'status' => 'pending',
        ]);

        $changeRequest = ChangeRequest::where('user_id', $user->id)->first();
        $this->assertNotNull($changeRequest->attachment_path);
        Storage::disk('local')->assertExists($changeRequest->attachment_path);

        Mail::assertSent(ChangeRequestMail::class, function ($mail) use ($changeRequest) {
            return $mail->changeRequest->id === $changeRequest->id;
        });
    }

    public function test_user_can_only_view_own_change_requests()
    {
        $user1 = User::factory()->create();
        $user1->assignRole('Committees');

        $user2 = User::factory()->create();
        $user2->assignRole('Committees');

        $req1 = ChangeRequest::create([
            'user_id' => $user1->id,
            'request_type' => 'general',
            'subject' => 'User 1 Request',
            'description' => 'Help me please',
            'status' => 'pending',
        ]);

        $req2 = ChangeRequest::create([
            'user_id' => $user2->id,
            'request_type' => 'general',
            'subject' => 'User 2 Request',
            'description' => 'Help me please too',
            'status' => 'pending',
        ]);

        $this->actingAs($user1);

        // Can view own request details
        $this->get(route('change-requests.show', $req1->id))
            ->assertStatus(200);

        // Cannot view other user's request details
        $this->get(route('change-requests.show', $req2->id))
            ->assertStatus(403);
    }

    public function test_super_admin_can_view_all_requests_and_update_status()
    {
        $user = User::factory()->create();
        $user->assignRole('Committees');

        $admin = User::factory()->create();
        $admin->assignRole('super admin');

        $req = ChangeRequest::create([
            'user_id' => $user->id,
            'request_type' => 'general',
            'subject' => 'User Request',
            'description' => 'Help me please',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        // Admin can view the request
        $this->get(route('change-requests.show', $req->id))
            ->assertStatus(200);

        // Admin can update the status
        $response = $this->patch(route('change-requests.update-status', $req->id), [
            'status' => 'in_progress',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('change_requests', [
            'id' => $req->id,
            'status' => 'in_progress',
        ]);
    }
}
