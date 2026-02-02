<?php

namespace Tests\Unit;

use App\Services\CatalogCache;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Unit tests for CatalogCache service.
 *
 * Tests verify versioned cache key generation and invalidation behavior.
 *
 * To run these tests:
 *   php artisan test --filter=CatalogCacheTest
 */
class CatalogCacheTest extends TestCase
{
    protected CatalogCache $cache;

    protected function setUp(): void
    {
        parent::setUp();
        // Use array driver for tests to avoid database dependency
        config(['cache.default' => 'array']);
        $this->cache = new CatalogCache;
        Cache::flush();
    }

    /**
     * Test that version starts at 1 when not set.
     */
    public function test_get_version_returns_1_by_default(): void
    {
        $this->assertEquals(1, $this->cache->getVersion());
    }

    /**
     * Test that invalidate increments the version.
     */
    public function test_invalidate_increments_version(): void
    {
        $this->assertEquals(1, $this->cache->getVersion());

        $this->cache->invalidate();
        $this->assertEquals(2, $this->cache->getVersion());

        $this->cache->invalidate();
        $this->assertEquals(3, $this->cache->getVersion());
    }

    /**
     * Test that cache key includes version number.
     */
    public function test_key_includes_version(): void
    {
        $key = $this->cache->key('abc123', 1);
        $this->assertStringContainsString(':v1:', $key);
        $this->assertStringContainsString('abc123', $key);
        $this->assertStringContainsString('page1', $key);
    }

    /**
     * Test that cache key changes after invalidation.
     */
    public function test_key_changes_after_invalidation(): void
    {
        $key1 = $this->cache->key('test', 1);
        $this->cache->invalidate();
        $key2 = $this->cache->key('test', 1);

        $this->assertNotEquals($key1, $key2);
        $this->assertStringContainsString(':v1:', $key1);
        $this->assertStringContainsString(':v2:', $key2);
    }

    /**
     * Test that remember stores and retrieves data.
     */
    public function test_remember_stores_and_retrieves_data(): void
    {
        $data = ['ids' => [1, 2, 3], 'total' => 3];

        $result = $this->cache->remember('hash', 1, fn () => $data);
        $this->assertEquals($data, $result);

        // Second call should return cached data
        $callCount = 0;
        $result2 = $this->cache->remember('hash', 1, function () use (&$callCount) {
            $callCount++;

            return ['different' => 'data'];
        });

        $this->assertEquals($data, $result2);
        $this->assertEquals(0, $callCount); // Callback should not be called
    }

    /**
     * Test that invalidation causes remember to fetch fresh data.
     */
    public function test_invalidation_causes_fresh_fetch(): void
    {
        $this->cache->remember('hash', 1, fn () => ['version' => 1]);

        $this->cache->invalidate();

        $result = $this->cache->remember('hash', 1, fn () => ['version' => 2]);
        $this->assertEquals(['version' => 2], $result);
    }

    /**
     * Test that getTtl returns expected value.
     */
    public function test_get_ttl_returns_positive_integer(): void
    {
        $ttl = $this->cache->getTtl();
        $this->assertIsInt($ttl);
        $this->assertGreaterThan(0, $ttl);
    }
}
