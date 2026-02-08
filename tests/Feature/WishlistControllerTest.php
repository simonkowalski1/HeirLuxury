<?php

// ABOUTME: Feature tests for the guest-only session-based wishlist API.
// ABOUTME: Covers toggle, count, ids, items endpoints, and session persistence.

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_toggle_adds_product_to_session(): void
    {
        $product = Product::factory()->create();

        $response = $this->postJson("/api/wishlist/toggle/{$product->id}");

        $response->assertOk();
        $response->assertJson(['wishlisted' => true, 'count' => 1]);
    }

    public function test_toggle_removes_product_from_session(): void
    {
        $product = Product::factory()->create();

        // Add first
        $this->postJson("/api/wishlist/toggle/{$product->id}");
        // Remove
        $response = $this->postJson("/api/wishlist/toggle/{$product->id}");

        $response->assertOk();
        $response->assertJson(['wishlisted' => false, 'count' => 0]);
    }

    public function test_toggle_returns_json_with_wishlisted_and_count(): void
    {
        $products = Product::factory()->count(3)->create();

        $this->postJson("/api/wishlist/toggle/{$products[0]->id}");
        $response = $this->postJson("/api/wishlist/toggle/{$products[1]->id}");

        $response->assertOk();
        $response->assertJsonStructure(['wishlisted', 'count']);
        $response->assertJson(['wishlisted' => true, 'count' => 2]);
    }

    public function test_count_returns_zero_for_empty_wishlist(): void
    {
        $response = $this->getJson('/api/wishlist/count');

        $response->assertOk();
        $response->assertJson(['count' => 0]);
    }

    public function test_count_returns_correct_count_after_adding(): void
    {
        $products = Product::factory()->count(3)->create();

        $this->postJson("/api/wishlist/toggle/{$products[0]->id}");
        $this->postJson("/api/wishlist/toggle/{$products[1]->id}");
        $this->postJson("/api/wishlist/toggle/{$products[2]->id}");

        $response = $this->getJson('/api/wishlist/count');

        $response->assertOk();
        $response->assertJson(['count' => 3]);
    }

    public function test_ids_returns_empty_array_initially(): void
    {
        $response = $this->getJson('/api/wishlist/ids');

        $response->assertOk();
        $response->assertJson(['ids' => []]);
    }

    public function test_ids_returns_product_ids_after_adding(): void
    {
        $products = Product::factory()->count(2)->create();

        $this->postJson("/api/wishlist/toggle/{$products[0]->id}");
        $this->postJson("/api/wishlist/toggle/{$products[1]->id}");

        $response = $this->getJson('/api/wishlist/ids');

        $response->assertOk();
        $response->assertJsonFragment(['ids' => [$products[0]->id, $products[1]->id]]);
    }

    public function test_items_returns_empty_for_no_wishlist(): void
    {
        $response = $this->getJson('/api/wishlist/items');

        $response->assertOk();
        $response->assertJson(['items' => []]);
    }

    public function test_items_returns_product_data_for_wishlisted_items(): void
    {
        $product = Product::factory()->create(['name' => 'LV Speedy Bag']);

        $this->postJson("/api/wishlist/toggle/{$product->id}");

        $response = $this->getJson('/api/wishlist/items');

        $response->assertOk();
        $response->assertJsonCount(1, 'items');
        $response->assertJsonFragment(['name' => 'LV Speedy Bag']);
    }

    public function test_wishlist_persists_across_requests(): void
    {
        $product = Product::factory()->create();

        // Add product in one request
        $this->postJson("/api/wishlist/toggle/{$product->id}");

        // Verify in a separate request (same session)
        $response = $this->getJson('/api/wishlist/ids');

        $response->assertOk();
        $response->assertJsonFragment(['ids' => [$product->id]]);
    }

    public function test_toggle_nonexistent_product_returns_404(): void
    {
        $response = $this->postJson('/api/wishlist/toggle/99999');

        $response->assertNotFound();
    }
}
