<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_committees', function (Blueprint $table) {
            if (Schema::hasColumn('service_committees', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('service_committees', 'email')) {
                // If email is a foreign key, drop it first
                try { $table->dropForeign(['email']); } catch (\Exception $e) {}
                $table->dropColumn('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_committees', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->nullable();
        });
    }
};