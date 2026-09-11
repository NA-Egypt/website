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
            $table->foreignId('parent_report_id')
                ->nullable()
                ->after('service_committee_id')
                ->constrained('committee_reports')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committee_reports', function (Blueprint $table) {
            $table->dropForeign(['parent_report_id']);
            $table->dropColumn('parent_report_id');
        });
    }
};
