<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Day;
use App\Models\ServiceBody;
use App\Models\ServiceCommittee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TaskTenImprovementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_helpline_api_schema_has_enum_options_and_store_returns_all_conditional_errors_together(): void
    {
        app()->setLocale('en');
        $schemaRes = $this->getJson('/api/v1/helpline-calls/schema');
        $schemaRes->assertStatus(200)
            ->assertJsonStructure([
                'caller_type_options' => [['value', 'label']],
                'referral_source_options' => [['value', 'label']],
            ]);

        // Missing caller_type_other, hospital_name, AND call_brief all at once
        $invalidRes = $this->postJson('/api/v1/helpline-calls', [
            'duration' => 'less_than_5',
            'call_date' => now()->toDateString(),
            'call_time_shift' => '8:00 PM - 10:00 PM',
            'caller_type' => 'other',
            'referral_source' => 'hospitals_committee',
            'volunteer_name' => 'other',
            'is_step_12' => true,
            'discuss_in_meeting' => false,
        ]);

        $invalidRes->assertStatus(422)
            ->assertJsonValidationErrors([
                'caller_type_other',
                'hospital_name',
                'volunteer_name_other',
                'call_brief',
            ]);

        // Valid enum submission stores canonical Arabic label
        $validRes = $this->postJson('/api/v1/helpline-calls', [
            'duration' => 'less_than_5',
            'call_date' => now()->toDateString(),
            'call_time_shift' => '8:00 PM - 10:00 PM',
            'caller_type' => 'current_member',
            'referral_source' => 'website',
            'volunteer_name' => 'أحمد',
            'is_step_12' => false,
            'discuss_in_meeting' => false,
        ]);

        $validRes->assertStatus(201)
            ->assertJsonPath('data.caller_type', 'عضو حالي')
            ->assertJsonPath('data.referral_source', 'الموقع الالكتروني');
    }

    public function test_api_v1_me_returns_user_roles_permissions_and_scope(): void
    {
        $this->getJson('/api/v1/me')->assertStatus(401);

        $day = Day::create(['ar_name' => 'السبت', 'en_name' => 'Saturday']);
        $sb = ServiceBody::create([
            'ar_name' => 'لجنة القاهرة',
            'en_name' => 'Cairo Area',
            'day_id' => $day->id,
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
            'location' => 'Cairo',
        ]);

        $role = Role::firstOrCreate(['name' => 'Committees', 'guard_name' => 'web']);
        $perm = Permission::firstOrCreate(['name' => 'manage own forms', 'guard_name' => 'web']);

        $user = User::factory()->create(['service_body_id' => $sb->id]);
        $user->assignRole($role);
        $user->givePermissionTo($perm);

        $committee = ServiceCommittee::create([
            'ar_name' => 'لجنة المستشفيات',
            'en_name' => 'H&I Committee',
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $res = $this->getJson('/api/v1/me');
        $res->assertStatus(200)
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('roles.0', 'Committees')
            ->assertJsonFragment(['manage own forms'])
            ->assertJsonPath('scope.service_body.id', $sb->id)
            ->assertJsonPath('scope.service_committee.id', $committee->id);
    }

    public function test_lookup_endpoint_performs_paginated_normalized_arabic_search(): void
    {
        $user = User::factory()->create();
        City::create(['ar_name' => 'إسكندرية', 'en_name' => 'Alexandria', 'user_id' => $user->id]);
        City::create(['ar_name' => 'القاهرة', 'en_name' => 'Cairo', 'user_id' => $user->id]);

        $res = $this->getJson('/lookup/cities?q='.urlencode('اسكندريه'));
        $res->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.ar_name', 'إسكندرية')
            ->assertJsonStructure(['data', 'meta', 'links']);

        $this->getJson('/lookup/unknown-entity')->assertStatus(404);
    }
}
