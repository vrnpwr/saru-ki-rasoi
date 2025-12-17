<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_variation_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_variation_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., Large, Small, Cheese
            $table->decimal('price', 8, 2)->default(0.00); // Additional cost
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_variation_options');
    }
};
