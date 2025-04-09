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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('model'); // Name of the model (e.g., Group, City)
            $table->string('operation'); // Action performed (e.g., create, update, delete)
            $table->json('details'); // Save model's attributes
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // User performing action
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
