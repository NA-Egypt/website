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
            // Rename ar_location back to location
            $table->renameColumn('ar_location', 'location');
            // Drop en_location
            $table->dropColumn('en_location');

            // Rename ar_group_type back to group_type
            $table->renameColumn('ar_group_type', 'group_type');
            // Drop en_group_type
            $table->dropColumn('en_group_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Rename location back to ar_location
            $table->renameColumn('location', 'ar_location');
            // Add en_location back
            $table->string('en_location')->after('ar_location');

            // Rename group_type back to ar_group_type
            $table->renameColumn('group_type', 'ar_group_type');
            // Add en_group_type back
            $table->string('en_group_type')->after('ar_group_type');
        });
    }
};
