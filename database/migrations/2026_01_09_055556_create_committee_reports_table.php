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
        Schema::create('committee_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_committee_id')->constrained('service_committees')->onDelete('cascade');
            $table->date('meeting_date');
            $table->string('meeting_day_description')->nullable();
            $table->longText('body')->nullable();
            $table->json('positions_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_reports');
    }
};
