<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $groupIds = [233, 238, 545, 546];
            $groups = DB::table('groups')->whereIn('id', $groupIds)->get();

            foreach ($groups as $group) {
                // Insert into direct_online_groups
                $directGroupId = DB::table('direct_online_groups')->insertGetId([
                    'ar_name' => $group->ar_name,
                    'en_name' => $group->en_name,
                    'ar_gsr_name' => $group->ar_gsr_name,
                    'en_gsr_name' => $group->en_gsr_name,
                    'phone' => $group->phone,
                    'location' => $group->location ?? '',
                    'user_id' => $group->user_id,
                    'created_at' => $group->created_at ?? now(),
                    'updated_at' => now(),
                ]);

                // Update associated meetings
                DB::table('meetings')
                    ->where('group_id', $group->id)
                    ->update([
                        'direct_online_group_id' => $directGroupId,
                        'group_id' => null,
                    ]);

                // Delete from groups
                DB::table('groups')->where('id', $group->id)->delete();
            }

            // Update user accounts to service_body_id = null
            $userIds = [73, 174, 212, 214];
            DB::table('users')->whereIn('id', $userIds)->update([
                'service_body_id' => null,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            $userIds = [73, 174, 212, 214];
            $directGroups = DB::table('direct_online_groups')->whereIn('user_id', $userIds)->get();

            foreach ($directGroups as $directGroup) {
                // Re-insert into groups table
                $groupId = DB::table('groups')->insertGetId([
                    'ar_name' => $directGroup->ar_name,
                    'en_name' => $directGroup->en_name,
                    'ar_gsr_name' => $directGroup->ar_gsr_name,
                    'en_gsr_name' => $directGroup->en_gsr_name,
                    'phone' => $directGroup->phone,
                    'location' => $directGroup->location,
                    'ar_address' => 'أونلاين',
                    'en_address' => 'Online',
                    'group_type' => 'اون لاين',
                    'service_body_id' => 2,
                    'neighborhood_id' => 86,
                    'user_id' => $directGroup->user_id,
                    'created_at' => $directGroup->created_at ?? now(),
                    'updated_at' => now(),
                ]);

                // Re-assign meetings back to groups
                DB::table('meetings')
                    ->where('direct_online_group_id', $directGroup->id)
                    ->update([
                        'group_id' => $groupId,
                        'direct_online_group_id' => null,
                    ]);

                // Delete from direct_online_groups
                DB::table('direct_online_groups')->where('id', $directGroup->id)->delete();
            }

            // Restore user accounts service_body_id = 2
            DB::table('users')->whereIn('id', $userIds)->update([
                'service_body_id' => 2,
            ]);
        });
    }
};
