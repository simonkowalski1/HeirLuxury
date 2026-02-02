<?php

// ABOUTME: Feature tests for the front-end ProductController (product detail page).
// ABOUTME: Covers product display, 404 handling, related products, and breadcrumbs.

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_product_page_returns_200_for_valid_product(): void
    {
        $product = Product::factory()->create([
            'category_slug' => 'louis-vuitton-women-bags',
            'slug' => 'neverfull-mm',
        ]);

        $response = $this->get('/en/catalog/louis-vuitton-women-bags/neverfull-mm');

        $response->assertOk();
    }

    public function test_product_page_returns_404_for_nonexistent_product(): void
    {
        $response = $this->get('/en/catalog/some-category/nonexistent-product');

        $response->assertNotFound();
    }

    public function test_product_page_passes_product_to_view(): void
    {
        $product = Product::factory()->create([
            'category_slug' => 'chanel-women-bags',
            'slug' => 'classic-flap',
            'name' => 'Classic Flap',
        ]);

        $response = $this->get('/en/catalog/chanel-women-bags/classic-flap');

        $response->assertOk();
        $response->assertViewHas('product', function ($viewProduct) use ($product) {
            return $viewProduct->id === $product->id;
        });
    }

    public function test_product_page_passes_breadcrumbs(): void
    {
        Product::factory()->create([
            'category_slug' => 'dior-women-bags',
            'slug' => 'lady-dior',
        ]);

        $response = $this->get('/en/catalog/dior-women-bags/lady-dior');

        $response->assertOk();
        $response->assertViewHas('breadcrumbs', function ($breadcrumbs) {
            return count($breadcrumbs) === 4
                && $breadcrumbs[0]['label'] === 'Home'
                && $breadcrumbs[1]['label'] === 'Catalog'
                && $breadcrumbs[3]['href'] === null; // Current page has no link
        });
    }

    public function test_product_page_passes_related_products(): void
    {
        $category = 'hermes-women-bags';

        $product = Product::factory()->create([
            'category_slug' => $category,
            'slug' => 'birkin-30',
        ]);

        // Create related products in the same category
        Product::factory()->count(5)->create([
            'category_slug' => $category,
        ]);

        $response = $this->get("/en/catalog/{$category}/birkin-30");

        $response->assertOk();
        $response->assertViewHas('related', function ($related) {
            return $related->count() === 5;
        });
    }

    public function test_product_page_limits_related_to_12(): void
    {
        $category = 'louis-vuitton-women-bags';

        $product = Product::factory()->create([
            'category_slug' => $category,
            'slug' => 'speedy-25',
        ]);

        // Create 15 related products
        Product::factory()->count(15)->create([
            'category_slug' => $category,
        ]);

        $response = $this->get("/en/catalog/{$category}/speedy-25");

        $response->assertOk();
        $response->assertViewHas('related', function ($related) {
            return $related->count() === 12;
        });
    }

    public function test_product_page_excludes_self_from_related(): void
    {
        $category = 'chanel-women-bags';

        $product = Product::factory()->create([
            'category_slug' => $category,
            'slug' => 'classic-flap',
        ]);

        Product::factory()->count(3)->create([
            'category_slug' => $category,
        ]);

        $response = $this->get("/en/catalog/{$category}/classic-flap");

        $response->assertOk();
        $response->assertViewHas('related', function ($related) use ($product) {
            return ! $related->contains('id', $product->id);
        });
    }
}
