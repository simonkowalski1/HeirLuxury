<?php

// ABOUTME: Factory for generating ProductImage test data.
// ABOUTME: Creates product gallery images with sequential sort ordering.

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'path' => 'products/gallery/'.fake()->uuid().'.jpg',
            'alt_text' => fake()->words(3, true),
            'sort_order' => 0,
            'is_primary' => false,
        ];
    }

    /**
     * Mark image as the primary image.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }
}
