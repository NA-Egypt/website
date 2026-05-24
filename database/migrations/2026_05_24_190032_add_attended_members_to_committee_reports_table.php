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
        Schema::table('committee_reports', function (Blueprint $table) {
            $table->text('attended_members')->nullable()->after('positions_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committee_reports', function (Blueprint $table) {
            $table->dropColumn('attended_members');
        });
    }
};
