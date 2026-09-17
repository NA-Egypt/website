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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('method', 10);
            $table->string('endpoint', 500);
            $table->string('route_name', 255)->nullable();
            $table->unsignedSmallInteger('status_code');
            $table->unsignedInteger('response_time_ms')->default(0);
            $table->string('platform', 30)->default('other'); // android, ios, web, other
            $table->string('app_version', 50)->nullable();
            $table->string('device_id', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            // Composite indexes for fast dashboard queries
            $table->index(['platform', 'created_at']);
            $table->index(['status_code', 'created_at']);
            $table->index(['endpoint', 'created_at']);
        });

        Schema::create('api_daily_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->index();
            $table->string('platform', 30)->default('all'); // all, android, ios, web, other
            $table->unsignedInteger('total_requests')->default(0);
            $table->unsignedInteger('successful_requests')->default(0);
            $table->unsignedInteger('client_error_requests')->default(0);
            $table->unsignedInteger('server_error_requests')->default(0);
            $table->float('avg_response_time_ms', 8, 2)->default(0);
            $table->unsignedInteger('unique_ips')->default(0);
            $table->unsignedInteger('unique_users')->default(0);
            $table->json('top_endpoints_json')->nullable();
            $table->timestamps();

            $table->unique(['date', 'platform']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_daily_stats');
        Schema::dropIfExists('api_logs');
    }
};
