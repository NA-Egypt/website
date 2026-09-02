<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions to define
        $permissions = [
            'view inventory slips',
            'acknowledge inventory slips',
            'view lit ledger',
            'create calendar events',
            'manage calendar events',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // Roles mapping
        $litUser = Role::firstOrCreate(['name' => 'Lit User', 'guard_name' => 'web']);
        $storeManager = Role::firstOrCreate(['name' => 'Store Manager', 'guard_name' => 'web']);
        $rsc = Role::firstOrCreate(['name' => 'rsc', 'guard_name' => 'web']);
        $committees = Role::firstOrCreate(['name' => 'Committees', 'guard_name' => 'web']);
        $serviceBody = Role::firstOrCreate(['name' => 'ServiceBody', 'guard_name' => 'web']);
        $superAdmin = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);

        // Assign permissions
        // Literature Committee (Lit User)
        $litUser->givePermissionTo([
            'view inventory slips',
            'acknowledge inventory slips',
            'view lit ledger',
            'create calendar events',
        ]);

        // Litstore Manager (Store Manager)
        $storeManager->givePermissionTo([
            'view inventory slips',
            'view lit ledger',
        ]);

        // RSC members
        $rsc->givePermissionTo([
            'view inventory slips',
            'view lit ledger',
            'create calendar events',
            'manage calendar events',
        ]);

        // Committees
        $committees->givePermissionTo([
            'create calendar events',
        ]);

        // Service Bodies
        $serviceBody->givePermissionTo([
            'create calendar events',
        ]);

        // Super Admin
        $superAdmin->givePermissionTo([
            'view inventory slips',
            'acknowledge inventory slips',
            'view lit ledger',
            'create calendar events',
            'manage calendar events',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view inventory slips',
            'acknowledge inventory slips',
            'view lit ledger',
            'create calendar events',
            'manage calendar events',
        ];

        foreach ($permissions as $permName) {
            $perm = Permission::where(['name' => $permName, 'guard_name' => 'web'])->first();
            if ($perm) {
                $perm->delete();
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
