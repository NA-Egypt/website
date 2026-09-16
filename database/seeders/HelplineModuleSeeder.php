<?php

namespace Database\Seeders;

use App\Models\HelplineVolunteer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HelplineModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create permission
        $perm = Permission::firstOrCreate([
            'name' => 'manage helpline',
            'guard_name' => 'web',
        ]);

        // 2. Assign to Super Admin role
        $superAdminRole = Role::where('name', 'super admin')->first();
        if ($superAdminRole && !$superAdminRole->hasPermissionTo($perm)) {
            $superAdminRole->givePermissionTo($perm);
        }

        // Assign to Phoneline role
        $phonelineRole = Role::where('name', 'Phoneline')->first();
        if ($phonelineRole && !$phonelineRole->hasPermissionTo($perm)) {
            $phonelineRole->givePermissionTo($perm);
        }

        // 3. Assign directly to target users
        $phoneUser = User::where('email', 'phone@naegypt.org')->first();
        if ($phoneUser && !$phoneUser->hasPermissionTo($perm)) {
            $phoneUser->givePermissionTo($perm);
        }

        $prUser = User::where('email', 'pr@naegypt.org')->first();
        if ($prUser && !$prUser->hasPermissionTo($perm)) {
            $prUser->givePermissionTo($perm);
        }

        // 4. Seed initial volunteers if table is empty
        if (HelplineVolunteer::count() === 0) {
            $volunteers = [
                ['name' => 'أحمد ع.', 'phone' => null, 'is_active' => true, 'sort_order' => 1],
                ['name' => 'محمد م.', 'phone' => null, 'is_active' => true, 'sort_order' => 2],
                ['name' => 'سارة ك.', 'phone' => null, 'is_active' => true, 'sort_order' => 3],
            ];

            foreach ($volunteers as $v) {
                HelplineVolunteer::create($v);
            }
        }
    }
}
