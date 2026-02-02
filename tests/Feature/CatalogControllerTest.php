<?php

// ABOUTME: Feature tests for the front-end CategoryController (catalog browsing).
// ABOUTME: Covers catalog index, category filtering, and infinite scroll API.

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    // ==================== Catalog Index ====================

    public function test_catalog_index_returns_200(): void
    {
        $response = $this->get('/en/catalog');

        $response->assertOk();
    }

    public function test_catalog_index_shows_products(): void
    {
        Product::factory()->count(5)->create();

        $response = $this->get('/en/catalog');

        $response->assertOk();
        $response->assertViewHas('products');
    }

    public function test_catalog_index_paginates_at_24_per_page(): void
    {
        Product::factory()->count(30)->create();

        $response = $this->get('/en/catalog');

        $response->assertOk();
        $response->assertViewHas('products', function ($products) {
            return $products->count() === 24;
        });
    }

    public function test_catalog_index_shows_empty_state(): void
    {
        $response = $this->get('/en/catalog');

        $response->assertOk();
        $response->assertViewHas('products', function ($products) {
            return $products->count() === 0;
        });
    }

    // ==================== Category Show ====================

    public function test_category_page_returns_200(): void
    {
        $response = $this->get('/en/catalog/women');

        $response->assertOk();
    }

    public function test_category_page_passes_title_to_view(): void
    {
        $response = $this->get('/en/catalog/women');

        $response->assertOk();
        $response->assertViewHas('title');
    }

    // ==================== API Endpoint ====================

    public function test_api_products_returns_json(): void
    {
        Product::factory()->count(5)->create();

        $response = $this->getJson('/api/catalog/products');

        $response->assertOk();
        $response->assertJsonStructure([
            'html',
            'hasMore',
            'nextPage',
            'total',
        ]);
    }

    public function test_api_products_respects_page_parameter(): void
    {
        Product::factory()->count(30)->create();

        $response = $this->getJson('/api/catalog/products?page=2');

        $response->assertOk();
        $response->assertJson(['nextPage' => 3]);
    }

    public function test_api_products_returns_empty_for_no_products(): void
    {
        $response = $this->getJson('/api/catalog/products');

        $response->assertOk();
        $response->assertJson([
            'hasMore' => false,
            'total' => 0,
        ]);
    }

    public function test_api_products_has_more_flag_is_correct(): void
    {
        Product::factory()->count(30)->create();

        $page1 = $this->getJson('/api/catalog/products?page=1');
        $page1->assertJson(['hasMore' => true]);

        $page2 = $this->getJson('/api/catalog/products?page=2');
        $page2->assertJson(['hasMore' => false]);
    }

    // ==================== Locale Support ====================

    public function test_catalog_works_with_polish_locale(): void
    {
        $response = $this->get('/pl/catalog');

        $response->assertOk();
    }
}
