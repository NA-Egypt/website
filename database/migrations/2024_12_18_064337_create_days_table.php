<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('en_name')->nullable();
            $table->string('ar_name')->nullable();
            $table->timestamps();
        });

        DB::table('days')->insert([
            ['name' => 'Saturday', 'en_name' => 'Saturday', 'ar_name' => 'السبت', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sunday', 'en_name' => 'Sunday', 'ar_name' => 'الأحد', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monday', 'en_name' => 'Monday', 'ar_name' => 'الاثنين', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tuesday', 'en_name' => 'Tuesday', 'ar_name' => 'الثلاثاء', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wednesday', 'en_name' => 'Wednesday', 'ar_name' => 'الأربعاء', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Thursday', 'en_name' => 'Thursday', 'ar_name' => 'الخميس', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Friday', 'en_name' => 'Friday', 'ar_name' => 'الجمعة', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
