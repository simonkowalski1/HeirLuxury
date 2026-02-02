<?php

// ABOUTME: Unit tests for the Product model.
// ABOUTME: Covers fillable attributes, relationships, and mass assignment security.

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_has_correct_fillable_attributes(): void
    {
        $product = new Product;

        $expected = [
            'name',
            'slug',
            'category_slug',
            'gender',
            'brand',
            'section',
            'folder',
            'image',
            'image_path',
        ];

        $this->assertEquals($expected, $product->getFillable());
    }

    public function test_product_belongs_to_category_via_slug(): void
    {
        $category = Category::factory()->create(['slug' => 'lv-women-bags']);
        $product = Product::factory()->create(['category_slug' => 'lv-women-bags']);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_product_category_returns_null_when_no_matching_category(): void
    {
        $product = Product::factory()->create(['category_slug' => 'nonexistent-slug']);

        $this->assertNull($product->category);
    }

    public function test_product_can_be_created_with_factory(): void
    {
        $product = Product::factory()->create();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->assertNotEmpty($product->name);
        $this->assertNotEmpty($product->slug);
        $this->assertNotEmpty($product->brand);
        $this->assertNotEmpty($product->gender);
        $this->assertNotEmpty($product->section);
    }

    public function test_product_for_brand_factory_state(): void
    {
        $product = Product::factory()->forBrand('Louis Vuitton')->create();

        $this->assertEquals('Louis Vuitton', $product->brand);
    }

    public function test_product_for_gender_factory_state(): void
    {
        $product = Product::factory()->forGender('women')->create();

        $this->assertEquals('women', $product->gender);
    }

    public function test_product_for_section_factory_state(): void
    {
        $product = Product::factory()->forSection('bags')->create();

        $this->assertEquals('bags', $product->section);
    }
}
