<?php

// ABOUTME: Unit tests for the ProductObserver.
// ABOUTME: Verifies cache invalidation on product create, update, and delete events.

namespace Tests\Unit;

use App\Models\Product;
use App\Services\CatalogCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_product_invalidates_catalog_cache(): void
    {
        $cache = app(CatalogCache::class);
        $versionBefore = $cache->getVersion();

        Product::factory()->create();

        $versionAfter = $cache->getVersion();

        $this->assertGreaterThan($versionBefore, $versionAfter);
    }

    public function test_updating_product_invalidates_catalog_cache(): void
    {
        $product = Product::factory()->create();

        $cache = app(CatalogCache::class);
        $versionBefore = $cache->getVersion();

        $product->update(['name' => 'Updated Name']);

        $versionAfter = $cache->getVersion();

        $this->assertGreaterThan($versionBefore, $versionAfter);
    }

    public function test_deleting_product_invalidates_catalog_cache(): void
    {
        $product = Product::factory()->create();

        $cache = app(CatalogCache::class);
        $versionBefore = $cache->getVersion();

        $product->delete();

        $versionAfter = $cache->getVersion();

        $this->assertGreaterThan($versionBefore, $versionAfter);
    }
}
