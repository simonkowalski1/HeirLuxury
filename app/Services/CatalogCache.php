<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Manages catalog cache with versioned invalidation.
 *
 * This service provides immediate cache invalidation when products change,
 * eliminating the 30-minute stale data window. Uses a version number in
 * cache keys so bumping the version instantly invalidates all catalog caches.
 *
 * Cache Strategy:
 * - Version stored in 'catalog_version' key (never expires)
 * - All catalog cache keys include version: "catalog:v{version}:..."
 * - On product change, increment version → old keys become orphaned
 * - Orphaned keys expire naturally via TTL
 *
 * Usage:
 *   $cache = app(CatalogCache::class);
 *   $ids = $cache->remember('women', $page, fn() => Product::pluck('id'));
 *   $cache->invalidate(); // Called from Product observer
 */
class CatalogCache
{
    /** @var int Cache TTL in seconds (30 minutes) */
    protected const TTL = 1800;

    /** @var string Cache key for version number */
    protected const VERSION_KEY = 'catalog_version';

    /**
     * Get the current cache version.
     *
     * @return int
     */
    public function getVersion(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }

    /**
     * Build a versioned cache key.
     *
     * @param string $slugsHash MD5 hash of category slugs (or 'all')
     * @param int $page Page number
     * @return string
     */
    public function key(string $slugsHash, int $page): string
    {
        $version = $this->getVersion();
        return "catalog:v{$version}:{$slugsHash}:page{$page}";
    }

    /**
     * Remember product IDs with versioned cache key.
     *
     * Caches only ID arrays (not full models) to reduce memory/serialization.
     *
     * @param string $slugsHash MD5 hash of category slugs (or 'all')
     * @param int $page Page number
     * @param callable $callback Returns array of product IDs
     * @return array{ids: array<int>, total: int, per_page: int, last_page: int}
     */
    public function remember(string $slugsHash, int $page, callable $callback): array
    {
        $key = $this->key($slugsHash, $page);

        return Cache::remember($key, self::TTL, $callback);
    }

    /**
     * Invalidate all catalog caches by bumping the version.
     *
     * Old cache entries become orphaned and expire via TTL.
     * Called from Product model observer on save/delete.
     */
    public function invalidate(): void
    {
        $current = $this->getVersion();
        Cache::forever(self::VERSION_KEY, $current + 1);
    }

    /**
     * Get the cache TTL in seconds.
     *
     * @return int
     */
    public function getTtl(): int
    {
        return self::TTL;
    }
}
