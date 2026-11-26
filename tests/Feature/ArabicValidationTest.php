<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArabicValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_api_request_in_ar_locale_returns_arabic_messages_with_friendly_labels(): void
    {
        app()->setLocale('ar');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));
        Sanctum::actingAs($superAdmin);

        $response = $this->postJson('/api/v1/meetings', [
            'start_time' => '18:00',
            'end_time' => '17:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['group_id', 'day_id', 'end_time']);

        $errors = $response->json('errors');

        $this->assertStringContainsString('المجموعة', $errors['group_id'][0]);
        $this->assertStringContainsString('مطلوب', $errors['group_id'][0]);

        $this->assertStringContainsString('اليوم', $errors['day_id'][0]);
        $this->assertStringContainsString('وقت الانتهاء', $errors['end_time'][0]);
        $this->assertStringNotContainsString('end_time', $errors['end_time'][0]);
    }

    public function test_invalid_api_request_in_en_locale_returns_english_messages_with_friendly_labels(): void
    {
        app()->setLocale('en');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));
        Sanctum::actingAs($superAdmin);

        $response = $this->postJson('/api/v1/users', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);

        $errors = $response->json('errors');

        $this->assertStringContainsString('name', $errors['name'][0]);
        $this->assertStringContainsString('email address', $errors['email'][0]);
    }
}
