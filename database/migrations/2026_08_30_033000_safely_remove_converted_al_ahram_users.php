<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $userIds = [73, 174, 212, 214];
            $adminUserId = 184;

            // 1. Reassign direct_online_groups.user_id to superadmin (184) to prevent CASCADE deletion
            DB::table('direct_online_groups')
                ->whereIn('user_id', $userIds)
                ->update(['user_id' => $adminUserId]);

            // 2. Set transactions.user_id = NULL to preserve historical audit logs
            DB::table('transactions')
                ->whereIn('user_id', $userIds)
                ->update(['user_id' => null]);

            // 3. Remove roles
            DB::table('model_has_roles')
                ->where('model_type', 'App\\Models\\User')
                ->whereIn('model_id', $userIds)
                ->delete();

            // 4. Remove legacy role user associations if table exists
            if (Schema::hasTable('legacy_role_user')) {
                DB::table('legacy_role_user')
                    ->whereIn('user_id', $userIds)
                    ->delete();
            }

            // 5. Delete users
            DB::table('users')->whereIn('id', $userIds)->delete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            $usersData = [
                [
                    'id' => 73,
                    'name' => 'elhadabaonline',
                    'display_name' => 'مجموعة الهضبة أونلاين',
                    'email' => 'elhadabaonline@naegypt.org',
                    'created_at' => null,
                    'updated_at' => '2026-06-14 15:06:29',
                    'service_body_id' => null,
                    'direct_group_id' => 11,
                ],
                [
                    'id' => 174,
                    'name' => 'Elegtma3online',
                    'display_name' => 'مجموعة الاجتماع أونلاين',
                    'email' => 'Elegtma3online@naegypt.org',
                    'created_at' => null,
                    'updated_at' => '2026-06-15 09:28:01',
                    'service_body_id' => null,
                    'direct_group_id' => 10,
                ],
                [
                    'id' => 212,
                    'name' => 'jft',
                    'display_name' => 'مجموعة لليوم فقط',
                    'email' => 'jft@naegypt.org',
                    'created_at' => '2026-02-19 00:19:02',
                    'updated_at' => '2026-06-14 15:24:36',
                    'service_body_id' => null,
                    'direct_group_id' => 13,
                ],
                [
                    'id' => 214,
                    'name' => 'elrehlaonline',
                    'display_name' => 'مجموعة الرحلة أونلاين',
                    'email' => 'elrehlaonline@naegypt.org',
                    'created_at' => '2026-03-29 17:04:51',
                    'updated_at' => '2026-06-14 15:37:12',
                    'service_body_id' => null,
                    'direct_group_id' => 12,
                ],
            ];

            foreach ($usersData as $u) {
                DB::table('users')->insert([
                    'id' => $u['id'],
                    'name' => $u['name'],
                    'display_name' => $u['display_name'],
                    'email' => $u['email'],
                    'created_at' => $u['created_at'],
                    'updated_at' => $u['updated_at'],
                    'service_body_id' => $u['service_body_id'],
                ]);

                DB::table('model_has_roles')->insert([
                    'role_id' => 2,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $u['id'],
                ]);

                DB::table('direct_online_groups')
                    ->where('id', $u['direct_group_id'])
                    ->update(['user_id' => $u['id']]);
            }
        });
    }
};
