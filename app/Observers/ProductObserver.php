<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\CatalogCache;

/**
 * Observer for Product model events.
 *
 * Automatically invalidates catalog cache when products are created,
 * updated, or deleted. This ensures users see changes immediately
 * instead of waiting for cache TTL to expire.
 *
 * Cache Invalidation Strategy:
 * - Bumps catalog version number on any product change
 * - All existing cache keys become orphaned (wrong version)
 * - Orphaned keys expire naturally via TTL
 * - New requests get fresh data with new version in key
 *
 * @see \App\Services\CatalogCache For cache management
 * @see \App\Providers\AppServiceProvider For observer registration
 */
class ProductObserver
{
    public function __construct(
        protected CatalogCache $catalogCache
    ) {}

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->catalogCache->invalidate();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->catalogCache->invalidate();
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->catalogCache->invalidate();
    }
}
