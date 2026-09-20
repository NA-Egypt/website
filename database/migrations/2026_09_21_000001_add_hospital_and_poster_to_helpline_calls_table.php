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
        Schema::table('helpline_calls', function (Blueprint $table) {
            $table->string('hospital_name', 255)->nullable()->after('referral_source_other');
            $table->string('poster_location', 255)->nullable()->after('hospital_name');
            $table->text('call_brief')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helpline_calls', function (Blueprint $table) {
            $table->dropColumn(['hospital_name', 'poster_location']);
            $table->text('call_brief')->nullable(false)->change();
        });
    }
};
