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
        Schema::create('committee_report_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('committee_report_id')->nullable()->index();
            $table->unsignedBigInteger('service_committee_id')->nullable()->index();
            $table->string('committee_name')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('action', 50)->index(); // created, updated, submitted, approved, returned_to_draft, deleted
            $table->string('summary');
            $table->json('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('service_committee_id')->references('id')->on('service_committees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_report_logs');
    }
};

