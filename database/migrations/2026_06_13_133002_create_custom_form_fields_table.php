<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_form_id')->constrained('custom_forms')->onDelete('cascade');
            $table->string('label');
            $table->string('type'); // text, number, email, date, textarea, select, checkbox, groups, cities, neighborhoods, committees, servicebodies
            $table->boolean('required')->default(false);
            $table->json('options')->nullable(); // dropdown or checkbox choices
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_form_fields');
    }
};
