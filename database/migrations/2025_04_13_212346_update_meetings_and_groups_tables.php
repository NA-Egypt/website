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
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->renameColumn('description', 'notes');
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->string('en_address')->nullable()->change();
            $table->string('location')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            // Revert changes for the meetings table
            Schema::table('meetings', function (Blueprint $table) {
                $table->string('title');                           // Add title back
                $table->renameColumn('notes', 'description');      // Rename notes back to description
            });

            // Revert changes for the groups table
            Schema::table('groups', function (Blueprint $table) {
                $table->string('location')->nullable(false)->change();    // Make location NOT NULL
                $table->string('en_address')->nullable(false)->change();  // Make en_address NOT NULL
            });
        });
    }
};
