<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['groups', 'direct_online_groups', 'cities', 'neighborhoods'] as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'search_text')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('search_text', 512)->nullable()->index();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['groups', 'direct_online_groups', 'cities', 'neighborhoods'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'search_text')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex(['search_text']);
                    $table->dropColumn('search_text');
                });
            }
        }
    }
};
