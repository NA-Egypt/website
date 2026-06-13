<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('survey'); // survey or event_registration
            $table->string('status')->default('draft'); // draft, published, unpublished
            $table->string('slug')->unique(); // unique automatic random URL
            $table->integer('views')->default(0);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_forms');
    }
};
