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
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->timestamps();
        });

        DB::table('options')->insert([
            ['name' => 'smoking', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'parking', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'candil', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'accessability', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
