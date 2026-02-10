<?php

// ABOUTME: Unit tests for the Category model.
// ABOUTME: Covers fillable attributes, factory, and basic operations.

namespace Tests\Unit;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_correct_fillable_attributes(): void
    {
        $category = new Category;

        $expected = ['name', 'slug', 'gender', 'section', 'brand', 'display_order', 'is_active'];

        $this->assertEquals($expected, $category->getFillable());
    }

    public function test_category_can_be_created_with_factory(): void
    {
        $category = Category::factory()->create();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertNotEmpty($category->name);
        $this->assertNotEmpty($category->slug);
    }

    public function test_category_slug_is_unique(): void
    {
        Category::factory()->create(['slug' => 'women-bags']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Category::factory()->create(['slug' => 'women-bags']);
    }

    public function test_category_name_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Category::create(['name' => null, 'slug' => 'test-slug']);
    }
}
