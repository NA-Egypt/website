<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update any existing standard groups in groups table that were marked as online to physical
        DB::table('groups')
            ->whereIn('group_type', ['اونلاين', 'اون لاين', 'online'])
            ->update(['group_type' => 'فعلي']);

        // 2. Remove the Online neighborhood
        DB::table('neighborhoods')
            ->where('ar_name', 'أونلاين')
            ->orWhere('en_name', 'Online')
            ->orWhere('id', 86)
            ->delete();

        // 3. Remove the Virtual city
        DB::table('cities')
            ->where('ar_name', 'افتراضي')
            ->orWhere('en_name', 'Virtual')
            ->orWhere('id', 24)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $cityId = DB::table('cities')->insertGetId([
            'id' => 24,
            'ar_name' => 'افتراضي',
            'en_name' => 'Virtual',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('neighborhoods')->insert([
            'id' => 86,
            'city_id' => $cityId,
            'ar_name' => 'أونلاين',
            'en_name' => 'Online',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
