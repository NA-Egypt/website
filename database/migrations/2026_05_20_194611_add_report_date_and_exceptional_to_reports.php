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
            $table->date('report_date')->nullable();
            $table->boolean('is_exceptional')->default(false);
        });

        // Populate existing reports' report_date with their created_at date
        \Illuminate\Support\Facades\DB::table('committee_reports')->update([
            'report_date' => \Illuminate\Support\Facades\DB::raw('DATE(created_at)')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committee_reports', function (Blueprint $table) {
            $table->dropColumn(['report_date', 'is_exceptional']);
        });
    }
};
