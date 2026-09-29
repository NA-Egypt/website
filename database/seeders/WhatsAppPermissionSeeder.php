<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class WhatsAppPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'manage whatsapp',
            'view whatsapp chats',
            'reply whatsapp messages',
        ];

        $created = [];
        foreach ($permissions as $name) {
            $created[$name] = Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // Grant all to super admin
        $superAdminRole = Role::where('name', 'super admin')->first();
        if ($superAdminRole) {
            foreach ($created as $p) {
                if (!$superAdminRole->hasPermissionTo($p)) {
                    $superAdminRole->givePermissionTo($p);
                }
            }
        }

        // Grant to admin role if present
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($created as $p) {
                if (!$adminRole->hasPermissionTo($p)) {
                    $adminRole->givePermissionTo($p);
                }
            }
        }

        // Grant view & reply to Helpline / Phoneline roles
        $phonelineRole = Role::where('name', 'Phoneline')->first();
        if ($phonelineRole) {
            if (!$phonelineRole->hasPermissionTo('view whatsapp chats')) {
                $phonelineRole->givePermissionTo('view whatsapp chats');
            }
            if (!$phonelineRole->hasPermissionTo('reply whatsapp messages')) {
                $phonelineRole->givePermissionTo('reply whatsapp messages');
            }
        }

        // Also assign directly to key phoneline users if they exist
        $phoneUser = User::where('email', 'phone@naegypt.org')->first();
        if ($phoneUser) {
            $phoneUser->givePermissionTo(['view whatsapp chats', 'reply whatsapp messages', 'manage whatsapp']);
        }
    }
}
