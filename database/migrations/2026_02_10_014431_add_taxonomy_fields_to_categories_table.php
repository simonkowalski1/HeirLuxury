<?php

// ABOUTME: Migration to add taxonomy fields (gender, section, brand, etc.) to categories.
// ABOUTME: Enables database-driven navigation instead of relying on config/categories.php.

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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('gender', 10)->nullable()->after('slug'); // women, men
            $table->string('section', 50)->nullable()->after('gender'); // bags, shoes, clothing, etc.
            $table->string('brand', 100)->nullable()->after('section'); // Louis Vuitton, Gucci, etc.
            $table->integer('display_order')->default(0)->after('brand');
            $table->boolean('is_active')->default(true)->after('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['gender', 'section', 'brand', 'display_order', 'is_active']);
        });
    }
};
