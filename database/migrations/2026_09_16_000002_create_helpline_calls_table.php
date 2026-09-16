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
        Schema::create('helpline_calls', function (Blueprint $table) {
            $table->id();
            $table->enum('duration', ['less_than_5', 'more_than_5']);
            $table->date('call_date');
            $table->string('call_time_shift', 100);
            $table->string('caller_type', 100);
            $table->string('caller_type_other')->nullable();
            $table->string('referral_source', 100);
            $table->string('referral_source_other')->nullable();
            $table->foreignId('volunteer_id')->nullable()->constrained('helpline_volunteers')->nullOnDelete();
            $table->string('volunteer_name', 150);
            $table->string('volunteer_name_other')->nullable();
            $table->boolean('is_step_12')->default(false);
            $table->text('call_brief');
            $table->boolean('discuss_in_meeting')->default(false);
            $table->text('additional_info')->nullable();
            $table->timestamp('entry_time')->useCurrent();
            $table->timestamps();

            $table->index('call_date');
            $table->index('entry_time');
            $table->index('duration');
            $table->index('is_step_12');
            $table->index('discuss_in_meeting');
            $table->index('call_time_shift');
            $table->index('caller_type');
            $table->index('referral_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpline_calls');
    }
};
