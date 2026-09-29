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
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('jid')->unique()->index();
            $table->string('phone')->nullable()->index();
            $table->string('name')->nullable();
            $table->boolean('is_live_agent_mode')->default(false)->index();
            $table->timestamp('live_agent_until')->nullable()->index();
            $table->foreignId('last_assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('current_step')->nullable()->index();
            $table->json('step_data')->nullable();
            $table->boolean('is_dev_test')->default(false)->index();
            $table->timestamp('last_interaction_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->string('message_id')->nullable()->index();
            $table->enum('direction', ['incoming', 'outgoing'])->index();
            $table->enum('sender_type', ['user', 'bot', 'agent'])->default('user')->index();
            $table->string('category')->nullable()->index();
            $table->string('message_type')->default('text')->index();
            $table->text('body')->nullable();
            $table->string('status')->default('received')->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('jid')->unique()->index();
            $table->string('phone')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('channel')->default('jft')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_dev_subscriber')->default(false)->index();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_broadcast_logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->default('jft')->index();
            $table->string('title');
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('successful_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->boolean('is_dev_broadcast')->default(false)->index();
            $table->foreignId('dispatched_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_broadcast_logs');
        Schema::dropIfExists('whatsapp_subscribers');
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
    }
};
