<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

return new class extends Migration {
    public function up(): void {
        $manageStore = Permission::firstOrCreate(['name' => 'manage store', 'guard_name' => 'web']);
        $viewLit = Permission::firstOrCreate(['name' => 'view lit inventory', 'guard_name' => 'web']);

        $storeManager = Role::firstOrCreate(['name' => 'Store Manager', 'guard_name' => 'web']);
        $storeManager->givePermissionTo($manageStore);

        $litUser = Role::firstOrCreate(['name' => 'Lit User', 'guard_name' => 'web']);
        $litUser->givePermissionTo($viewLit);
        
        // Assign to Super Admin
        $superAdmin = Role::where('name', 'super admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo([$manageStore, $viewLit]);
        }

        // Check for litstore@naegypt.org
        $storeUser = User::where('email', 'litstore@naegypt.org')->first();
        if ($storeUser) {
            $storeUser->assignRole($storeManager);
        } else {
            // Create user for local testing if not existing
            $testStoreUser = User::firstOrCreate([
                'email' => 'litstore@naegypt.org',
            ], [
                'name' => 'Lit Store Manager',
                'display_name' => 'Lit Store Manager',
                'password' => bcrypt('Password123!'),
            ]);
            $testStoreUser->assignRole($storeManager);
        }

        // Check for lit@naegypt.org
        $literatureUser = User::where('email', 'lit@naegypt.org')->first();
        if ($literatureUser) {
            $literatureUser->assignRole($litUser);
        } else {
            // Create user for local testing if not existing
            $testLitUser = User::firstOrCreate([
                'email' => 'lit@naegypt.org',
            ], [
                'name' => 'Lit User',
                'display_name' => 'Lit User',
                'password' => bcrypt('Password123!'),
            ]);
            $testLitUser->assignRole($litUser);
        }
    }

    public function down(): void {
        // Safe to leave empty
    }
};
