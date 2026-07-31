<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stocktaking_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('in_progress'); // in_progress, completed, adjusted
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('adjusted_at')->nullable();
            $table->foreignId('adjusted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('stocktaking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stocktaking_session_id')->constrained('stocktaking_sessions')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->integer('system_store_qty')->default(0);
            $table->integer('system_lit_qty')->default(0);
            $table->integer('counted_store_qty')->nullable();
            $table->integer('counted_lit_qty')->nullable();
            $table->integer('store_variance')->default(0);
            $table->integer('lit_variance')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->decimal('variance_value', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('stocktaking_items');
        Schema::dropIfExists('stocktaking_sessions');
    }
};
