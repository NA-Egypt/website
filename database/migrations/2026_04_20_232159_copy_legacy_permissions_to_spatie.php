<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('legacy_roles')) {
            return;
        }

        // Copy roles
        $roles = DB::table('legacy_roles')->get();
        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore([
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => 'web',
                'created_at' => $role->created_at ?? now(),
                'updated_at' => $role->updated_at ?? now(),
            ]);
        }

        // Copy permissions
        $permissions = DB::table('legacy_permissions')->get();
        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore([
                'id' => $permission->id,
                'name' => $permission->name,
                'guard_name' => 'web',
                'created_at' => $permission->created_at ?? now(),
                'updated_at' => $permission->updated_at ?? now(),
            ]);
        }

        // Copy model_has_roles
        $roleUsers = DB::table('legacy_role_user')->get();
        foreach ($roleUsers as $ru) {
            DB::table('model_has_roles')->insertOrIgnore([
                'role_id' => $ru->role_id,
                'model_type' => 'App\Models\User',
                'model_id' => $ru->user_id,
            ]);
        }

        // Copy role_has_permissions
        $permissionRoles = DB::table('legacy_permission_role')->get();
        foreach ($permissionRoles as $pr) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $pr->permission_id,
                'role_id' => $pr->role_id,
            ]);
        }
    }

    public function down(): void
    {
        // Down migration can be empty, we just clear Spatie tables
        // To be safe, wait, there's no need to truncate on down in production unless rollbacks are strictly done.
    }
};
