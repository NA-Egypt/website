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
            $table->string('ar_address')->nullable()->change();
            $table->string('en_address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_committees', function (Blueprint $table) {
            $table->string('ar_address')->nullable(false)->change();
            $table->string('en_address')->nullable(false)->change();
        });
    }
};
