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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->string('name');

            // Taxonomy
            $table->string('category_slug');   // e.g. "louis-vuitton-women-bags"
            $table->string('gender')->nullable();  // "women" | "men"
            $table->string('brand')->nullable();   // "Louis Vuitton"
            $table->string('section')->nullable(); // "Bags", "Shoes", etc.
            $table->string('folder')->nullable();  // import folder name, if you want it

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
