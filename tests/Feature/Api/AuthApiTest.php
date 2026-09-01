<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_azure_login_validates_required_access_token(): void
    {
        $response = $this->postJson('/api/v1/auth/azure/login', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['access_token']);

        $aliasResponse = $this->postJson('/api/v1/login/azure', []);
        $aliasResponse->assertStatus(422)
            ->assertJsonValidationErrors(['access_token']);
    }

    public function test_azure_login_returns_401_on_invalid_azure_token(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');
        $provider->shouldReceive('userFromToken')
            ->with('invalid-token')
            ->andThrow(new \Exception('Invalid token'));

        Socialite::shouldReceive('driver')->with('azure')->andReturn($provider);

        $response = $this->postJson('/api/v1/auth/azure/login', [
            'access_token' => 'invalid-token',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid Azure Token']);
    }

    public function test_azure_login_exchanges_valid_token_and_issues_sanctum_token(): void
    {
        $socialiteUser = new SocialiteUser();
        $socialiteUser->map([
            'id' => '12345',
            'name' => 'John Doe',
            'email' => 'john.doe@naegypt.org',
        ]);

        $provider = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');
        $provider->shouldReceive('userFromToken')
            ->with('valid-azure-token')
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('azure')->andReturn($provider);

        $response = $this->postJson('/api/v1/auth/azure/login', [
            'access_token' => 'valid-azure-token',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'token',
            ])
            ->assertJsonPath('user.email', 'john.doe@naegypt.org')
            ->assertJsonPath('user.name', 'John Doe');

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@naegypt.org',
            'name' => 'John Doe',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_profile_endpoint(): void
    {
        $response = $this->getJson('/api/v1/user');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_profile_endpoint(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Member',
            'email' => 'jane.member@naegypt.org',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/user');
        $response->assertStatus(200)
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('name', 'Jane Member')
            ->assertJsonPath('email', 'jane.member@naegypt.org');
    }
}
