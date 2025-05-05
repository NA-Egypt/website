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
        Schema::create('sc_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_committee_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('week_number');
            $table->foreignId('day_id')->constrained('days')->onDelete('cascade');
            $table->time('time');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sc_meetings');
    }
};
