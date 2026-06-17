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
        Schema::create('service_body_agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_body_id')->constrained()->cascadeOnDelete();
            $table->date('agenda_date');
            $table->date('meeting_date');
            $table->json('groups_joined')->nullable();
            $table->json('body');
            $table->string('status')->default('draft'); // draft, submitted, approved
            $table->boolean('is_exceptional')->default(false);
            $table->timestamps();
        });

        Schema::table('service_bodies', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('recurrence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bodies', function (Blueprint $table) {
            $table->dropColumn('logo');
        });

        Schema::dropIfExists('service_body_agendas');
    }
};
