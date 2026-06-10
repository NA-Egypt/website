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
        Schema::table('service_committees', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('notes');
            $table->text('default_footer')->nullable()->after('logo');
        });

        Schema::table('committee_reports', function (Blueprint $table) {
            $table->text('footer')->nullable()->after('attended_members');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committee_reports', function (Blueprint $table) {
            $table->dropColumn('footer');
        });

        Schema::table('service_committees', function (Blueprint $table) {
            $table->dropColumn(['logo', 'default_footer']);
        });
    }
};
