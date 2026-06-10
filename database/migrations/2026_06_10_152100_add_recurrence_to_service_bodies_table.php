<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_bodies', function (Blueprint $table) {
            $table->json('recurrence')->nullable();
        });

        // Update existing service bodies to have default recurrence ['1st', 'monthly']
        \Illuminate\Support\Facades\DB::table('service_bodies')->update([
            'recurrence' => json_encode(['1st', 'monthly'])
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bodies', function (Blueprint $table) {
            $table->dropColumn('recurrence');
        });
    }
};
