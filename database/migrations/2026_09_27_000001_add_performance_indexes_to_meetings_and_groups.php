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
        Schema::table('meetings', function (Blueprint $table) {
            $table->index(['status', 'day_id', 'type'], 'idx_meetings_status_day_type');
            $table->index(['group_id', 'status'], 'idx_meetings_group_status');
            $table->index(['direct_online_group_id', 'status'], 'idx_meetings_direct_status');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->index(['neighborhood_id', 'group_type'], 'idx_groups_neighborhood_type');
            $table->index(['service_body_id'], 'idx_groups_service_body');
        });

        Schema::table('neighborhoods', function (Blueprint $table) {
            $table->index(['city_id'], 'idx_neighborhoods_city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropIndex('idx_meetings_status_day_type');
            $table->dropIndex('idx_meetings_group_status');
            $table->dropIndex('idx_meetings_direct_status');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropIndex('idx_groups_neighborhood_type');
            $table->dropIndex('idx_groups_service_body');
        });

        Schema::table('neighborhoods', function (Blueprint $table) {
            $table->dropIndex('idx_neighborhoods_city_id');
        });
    }
};
