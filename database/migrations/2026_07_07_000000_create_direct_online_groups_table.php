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
        Schema::create('direct_online_groups', function (Blueprint $table) {
            $table->id();
            $table->string('ar_name');
            $table->string('en_name');
            $table->string('ar_gsr_name')->nullable();
            $table->string('en_gsr_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('location');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->change();
            $table->foreignId('direct_online_group_id')->nullable()->constrained('direct_online_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign(['direct_online_group_id']);
            $table->dropColumn('direct_online_group_id');
            $table->foreignId('group_id')->nullable(false)->change();
        });

        Schema::dropIfExists('direct_online_groups');
    }
};
