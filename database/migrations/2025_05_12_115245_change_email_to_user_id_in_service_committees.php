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


            if (Schema::hasColumn('service_committees', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }


            if (Schema::hasColumn('service_committees', 'email')) {
                $table->dropForeign(['email']);
                $table->dropColumn('email');
            }


            $table->unsignedBigInteger('email')->after('id');
            $table->foreign('email')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_committees', function (Blueprint $table) {
            //
        });
    }
};
