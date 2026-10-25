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

        foreach (['Treasurer', 'ServiceBody'] as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->givePermissionTo([
                'manage literature requests',
                'edit literature requests',
            ]);
        }
    }

    public function down(): void {
        // Safe to leave empty
    }
};
