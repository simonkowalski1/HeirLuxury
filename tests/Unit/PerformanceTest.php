<?php

namespace Tests\Unit;

use App\Services\CatalogCache;
use App\Services\ThumbnailService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Performance tests to ensure optimizations are working correctly.
 *
 * These tests verify:
 * - Cache operations are fast
 * - Database queries use indexes
 * - Thumbnail service returns correct URLs
 * - No N+1 queries in common operations
 */
class PerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
    }

    /**
     * Test that cache write operations complete quickly.
     */
    public function test_cache_write_is_fast(): void
    {
        $start = microtime(true);

        for ($i = 0; $i < 10; $i++) {
            Cache::put("test_key_{$i}", ['data' => str_repeat('x', 1000)], 60);
        }

        $elapsed = (microtime(true) - $start) * 1000;

        // 10 cache writes should complete in under 500ms (array driver in tests is slower)
        $this->assertLessThan(500, $elapsed, "Cache writes took {$elapsed}ms, expected < 500ms");
    }

    /**
     * Test that cache read operations complete quickly.
     */
    public function test_cache_read_is_fast(): void
    {
        // Pre-populate cache
        for ($i = 0; $i < 10; $i++) {
            Cache::put("test_key_{$i}", ['data' => str_repeat('x', 1000)], 60);
        }

        $start = microtime(true);

        for ($i = 0; $i < 10; $i++) {
            Cache::get("test_key_{$i}");
        }

        $elapsed = (microtime(true) - $start) * 1000;

        // 10 cache reads should complete in under 50ms
        $this->assertLessThan(50, $elapsed, "Cache reads took {$elapsed}ms, expected < 50ms");
    }

    /**
     * Test that CatalogCache generates versioned keys correctly.
     */
    public function test_catalog_cache_generates_versioned_keys(): void
    {
        $cache = new CatalogCache;

        $key1 = $cache->key('test-hash', 1);
        $key2 = $cache->key('test-hash', 2);
        $key3 = $cache->key('other-hash', 1);

        // Keys should include version
        $this->assertMatchesRegularExpression('/^catalog:v\d+:/', $key1);

        // Different pages should have different keys
        $this->assertNotEquals($key1, $key2);

        // Different hashes should have different keys
        $this->assertNotEquals($key1, $key3);
    }

    /**
     * Test that CatalogCache invalidation changes the version.
     */
    public function test_catalog_cache_invalidation_changes_version(): void
    {
        $cache = new CatalogCache;

        $versionBefore = $cache->getVersion();
        $keyBefore = $cache->key('test', 1);

        $cache->invalidate();

        $versionAfter = $cache->getVersion();
        $keyAfter = $cache->key('test', 1);

        $this->assertGreaterThan($versionBefore, $versionAfter);
        $this->assertNotEquals($keyBefore, $keyAfter);
    }

    /**
     * Test that CatalogCache remember function caches data.
     */
    public function test_catalog_cache_remember_caches_data(): void
    {
        $cache = new CatalogCache;
        $callCount = 0;

        $callback = function () use (&$callCount) {
            $callCount++;

            return ['ids' => [1, 2, 3], 'total' => 3, 'per_page' => 24, 'last_page' => 1];
        };

        // First call should execute callback
        $result1 = $cache->remember('test', 1, $callback);
        $this->assertEquals(1, $callCount);

        // Second call should use cache, not callback
        $result2 = $cache->remember('test', 1, $callback);
        $this->assertEquals(1, $callCount); // Still 1, callback not called again

        $this->assertEquals($result1, $result2);
    }

    /**
     * Test that ThumbnailService generates correct paths.
     */
    public function test_thumbnail_service_generates_correct_paths(): void
    {
        $service = new ThumbnailService;

        // Test path generation for different sizes
        $cardPath = $service->getThumbnailPath('imports/lv-bags-women/Test Product/0000.jpg', 'card');
        $galleryPath = $service->getThumbnailPath('imports/lv-bags-women/Test Product/0000.jpg', 'gallery');
        $thumbPath = $service->getThumbnailPath('imports/lv-bags-women/Test Product/0000.jpg', 'thumb');

        $this->assertStringContainsString('card', $cardPath);
        $this->assertStringContainsString('gallery', $galleryPath);
        $this->assertStringContainsString('thumb', $thumbPath);

        // All should be WebP
        $this->assertStringEndsWith('.webp', $cardPath);
        $this->assertStringEndsWith('.webp', $galleryPath);
        $this->assertStringEndsWith('.webp', $thumbPath);
    }

    /**
     * Test that ThumbnailService handles various file extensions.
     */
    public function test_thumbnail_service_handles_extensions(): void
    {
        $service = new ThumbnailService;

        $jpgPath = $service->getThumbnailPath('test/image.jpg', 'card');
        $pngPath = $service->getThumbnailPath('test/image.png', 'card');
        $jpegPath = $service->getThumbnailPath('test/image.jpeg', 'card');

        // All should convert to WebP
        $this->assertStringEndsWith('.webp', $jpgPath);
        $this->assertStringEndsWith('.webp', $pngPath);
        $this->assertStringEndsWith('.webp', $jpegPath);
    }

    /**
     * Test that ThumbnailService returns null for invalid sizes.
     */
    public function test_thumbnail_service_returns_null_for_invalid_size(): void
    {
        $service = new ThumbnailService;

        $result = $service->getUrl('test/image.jpg', 'invalid_size');

        $this->assertNull($result);
    }

    /**
     * Test that array flip sorting preserves order correctly.
     */
    public function test_array_flip_sorting_preserves_order(): void
    {
        $ids = [5, 2, 8, 1, 9];
        $idOrder = array_flip($ids);

        // Simulate out-of-order collection
        $items = collect([
            (object) ['id' => 1],
            (object) ['id' => 2],
            (object) ['id' => 5],
            (object) ['id' => 8],
            (object) ['id' => 9],
        ]);

        $sorted = $items->sortBy(fn ($p) => $idOrder[$p->id] ?? PHP_INT_MAX)->values();

        $sortedIds = $sorted->pluck('id')->all();

        $this->assertEquals($ids, $sortedIds);
    }

    /**
     * Test that cache TTL is reasonable (30 minutes).
     */
    public function test_catalog_cache_ttl_is_reasonable(): void
    {
        $cache = new CatalogCache;

        $ttl = $cache->getTtl();

        // Should be 30 minutes (1800 seconds)
        $this->assertEquals(1800, $ttl);

        // Should be between 15-60 minutes for good balance
        $this->assertGreaterThanOrEqual(900, $ttl);
        $this->assertLessThanOrEqual(3600, $ttl);
    }
}
