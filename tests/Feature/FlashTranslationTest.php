<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FlashTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_flash_messages_are_translated_in_ar_and_en(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

        app()->setLocale('ar');
        $responseAr = $this->actingAs($admin)->post(route('direct-online-group.store'), [
            'ar_name' => 'مجموعة أونلاين',
            'en_name' => 'Online Group',
            'ar_gsr_name' => 'أحمد',
            'en_gsr_name' => 'Ahmed',
            'phone' => '+201000000000',
            'location' => 'https://zoom.us/j/123',
        ]);
        $responseAr->assertRedirect()
            ->assertSessionHas('success', __('messages.flash.direct_online_groups.created'));
        $this->assertNotEquals('Group created successfully.', session('success'));

        app()->setLocale('en');
        $responseEn = $this->actingAs($admin)->post(route('direct-online-group.store'), [
            'ar_name' => 'مجموعة أونلاين 2',
            'en_name' => 'Online Group 2',
            'ar_gsr_name' => 'أحمد',
            'en_gsr_name' => 'Ahmed',
            'phone' => '+201000000001',
            'location' => 'https://zoom.us/j/456',
        ]);
        $responseEn->assertRedirect()
            ->assertSessionHas('success', 'Group created successfully.');
    }
}
