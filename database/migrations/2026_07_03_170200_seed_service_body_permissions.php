<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    public function up(): void {
        $permissions = [
            'create sb agenda',
            'edit sb agenda',
            'approve sb agenda',
            'delete sb agenda',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Assign to Super Admin
        $superAdmin = Role::where('name', 'super admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // Assign to RSC
        $rsc = Role::where('name', 'rsc')->first();
        if ($rsc) {
            $rsc->givePermissionTo($permissions);
        }
    }

    public function down(): void {
        // Safe to leave empty
    }
};
