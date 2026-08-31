<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_slips', function (Blueprint $table) {
            $table->id();
            $table->string('slip_number')->unique(); // e.g. TR-202608-0001, RT-202608-0001
            $table->string('type'); // 'transfer_to_lit', 'return_to_store'
            $table->string('status')->default('transferred'); // 'transferred', 'received', 'completed'
            $table->foreignId('issued_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('received_at')->nullable();
            $table->integer('total_items_count')->default(0);
            $table->decimal('total_value', 12, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_slip_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_slip_id')->constrained('inventory_slips')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->decimal('total_price', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventory_slip_items');
        Schema::dropIfExists('inventory_slips');
    }
};
