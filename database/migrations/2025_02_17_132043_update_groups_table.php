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
        Schema::table('groups', function (Blueprint $table) {
            $table->renameColumn('location', 'ar_location');
            $table->string('en_location')->after('ar_location');
            $table->renameColumn('gsr_name', 'ar_gsr_name');
            $table->string('en_gsr_name')->after('ar_gsr_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->renameColumn('ar_location', 'location');
            $table->dropColumn('en_location');
            $table->renameColumn('ar_gsr_name', 'gsr_name');
            $table->dropColumn('en_gsr_name');
        });
    }
};
