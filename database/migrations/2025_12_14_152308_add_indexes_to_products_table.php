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
        Schema::table('products', function (Blueprint $table) {
            $table->index('category_slug');
            $table->index(['category_slug', 'slug']);
            $table->index('brand');
            $table->index('gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_slug']);
            $table->dropIndex(['category_slug', 'slug']);
            $table->dropIndex(['brand']);
            $table->dropIndex(['gender']);
        });
    }
};
