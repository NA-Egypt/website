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
            $table->string('ar_address')->after('en_name');
            $table->string('en_address')->after('ar_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_committees', function (Blueprint $table) {
            $table->dropColumn(['ar_address', 'en_address']);
        });
    }
};
