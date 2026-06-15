<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permission = Permission::firstOrCreate(['name' => 'manage own forms', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'Form Builder', 'guard_name' => 'web']);
        
        $role->givePermissionTo($permission);

        // Also assign the permission to existing roles: Committees, ServiceBody, rsc
        foreach (['Committees', 'ServiceBody', 'rsc'] as $roleName) {
            $existingRole = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($existingRole) {
                $existingRole->givePermissionTo($permission);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::where('name', 'manage own forms')->where('guard_name', 'web')->first();
        
        if ($permission) {
            // Revoke permission from roles before deleting
            foreach (['Committees', 'ServiceBody', 'rsc', 'Form Builder'] as $roleName) {
                $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
                if ($role) {
                    $role->revokePermissionTo($permission);
                }
            }
            $permission->delete();
        }

        $role = Role::where('name', 'Form Builder')->where('guard_name', 'web')->first();
        if ($role) {
            $role->delete();
        }
    }
};
