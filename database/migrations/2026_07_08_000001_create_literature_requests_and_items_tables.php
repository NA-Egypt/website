<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('literature_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('cascade');
            $table->foreignId('service_body_id')->constrained('service_bodies')->onDelete('cascade');
            $table->date('month');
            $table->string('status')->default('draft'); // draft, submitted, sent_to_committee, returned_by_committee
            $table->string('type')->default('group'); // group, servicebody
            $table->integer('total_items_count')->default(0);
            $table->decimal('total_price', 10, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('literature_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('literature_request_id')->constrained('literature_requests')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('literature_request_items');
        Schema::dropIfExists('literature_requests');
    }
};
