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
        Schema::table('whatsapp_broadcast_logs', function (Blueprint $table) {
            $table->string('device_id')->default('default')->after('channel');
            $table->string('status')->default('completed')->index()->after('failed_count');
            $table->string('anti_ban_profile')->nullable()->after('status');
            $table->json('metadata')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_broadcast_logs', function (Blueprint $table) {
            $table->dropColumn(['device_id', 'status', 'anti_ban_profile', 'metadata']);
        });
    }
};
