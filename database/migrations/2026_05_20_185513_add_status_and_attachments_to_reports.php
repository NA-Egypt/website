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
            $table->string('status')->default('draft')->after('positions_status');
        });

        // Set all existing reports to submitted status
        \Illuminate\Support\Facades\DB::table('committee_reports')->update(['status' => 'submitted']);

        Schema::create('committee_report_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_report_id')->constrained('committee_reports')->onDelete('cascade');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_report_attachments');

        Schema::table('committee_reports', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
