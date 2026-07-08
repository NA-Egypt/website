<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    public function up(): void {
        $permissions = [
            'manage literature requests',
            'approve literature requests',
            'edit literature requests',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $treasurer = Role::firstOrCreate(['name' => 'Treasurer', 'guard_name' => 'web']);
        $treasurer->givePermissionTo([
            'manage literature requests',
            'approve literature requests',
        ]);

        // Lit User / Store Manager need access to edit/view requests too
        $litUser = Role::where('name', 'Lit User')->first();
        if ($litUser) {
            $litUser->givePermissionTo([
                'manage literature requests',
                'edit literature requests',
            ]);
        }

        $storeManager = Role::where('name', 'Store Manager')->first();
        if ($storeManager) {
            $storeManager->givePermissionTo([
                'manage literature requests',
                'edit literature requests',
            ]);
        }

        // Assign to Super Admin
        $superAdmin = Role::where('name', 'super admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }
    }

    public function down(): void {
        // Safe to leave empty
    }
};
