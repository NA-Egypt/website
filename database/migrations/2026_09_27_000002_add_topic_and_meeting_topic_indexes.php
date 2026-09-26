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
        Schema::table('topics', function (Blueprint $table) {
            $table->index('en_name', 'idx_topics_en_name');
        });

        Schema::table('meeting_topic', function (Blueprint $table) {
            $table->unique(['meeting_id', 'topic_id'], 'idx_meeting_topic_unique');
            $table->index(['topic_id', 'meeting_id'], 'idx_topic_meeting');
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->index('lang', 'idx_meetings_lang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropIndex('idx_topics_en_name');
        });

        Schema::table('meeting_topic', function (Blueprint $table) {
            $table->dropIndex('idx_meeting_topic_unique');
            $table->dropIndex('idx_topic_meeting');
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->dropIndex('idx_meetings_lang');
        });
    }
};
