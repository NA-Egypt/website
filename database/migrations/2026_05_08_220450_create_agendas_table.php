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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->integer('meetings_per_week')->nullable();
            $table->date('agenda_date');
            $table->string('service_position');
            $table->string('submitter_name')->nullable();
            $table->string('alt_gsr_position')->nullable();
            $table->string('alt_gsr_name')->nullable();
            $table->integer('new_comers')->nullable();
            $table->text('open_positions')->nullable();
            $table->dateTime('next_business_meeting')->nullable();
            $table->boolean('recovery_meetings_changes')->default(false);
            $table->text('recovery_atmosphere')->nullable();
            $table->text('trusted_servants')->nullable();
            $table->text('financial_issues')->nullable();
            $table->text('other_topics')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
