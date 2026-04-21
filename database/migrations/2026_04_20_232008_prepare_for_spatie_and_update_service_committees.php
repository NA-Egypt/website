<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename existing custom role tables
        if (Schema::hasTable('roles')) { Schema::rename('roles', 'legacy_roles'); }
        if (Schema::hasTable('permissions')) { Schema::rename('permissions', 'legacy_permissions'); }
        if (Schema::hasTable('role_user')) { Schema::rename('role_user', 'legacy_role_user'); }
        if (Schema::hasTable('permission_role')) { Schema::rename('permission_role', 'legacy_permission_role'); }

        // 2. Add user_id to service_committees
        if (!Schema::hasColumn('service_committees', 'user_id')) {
            Schema::table('service_committees', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            });

            // Map emails to user_id
            $committees = DB::table('service_committees')->whereNotNull('email')->get();
            foreach ($committees as $committee) {
                $user = DB::table('users')->where('email', $committee->email)->first();
                if ($user) {
                    DB::table('service_committees')->where('id', $committee->id)->update(['user_id' => $user->id]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('service_committees', 'user_id')) {
            Schema::table('service_committees', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasTable('legacy_permission_role')) { Schema::rename('legacy_permission_role', 'permission_role'); }
        if (Schema::hasTable('legacy_role_user')) { Schema::rename('legacy_role_user', 'role_user'); }
        if (Schema::hasTable('legacy_permissions')) { Schema::rename('legacy_permissions', 'permissions'); }
        if (Schema::hasTable('legacy_roles')) { Schema::rename('legacy_roles', 'roles'); }
    }
};
