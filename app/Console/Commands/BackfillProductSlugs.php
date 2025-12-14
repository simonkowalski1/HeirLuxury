<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Str;

/**
 * Backfill URL slugs for existing products.
 *
 * This maintenance command generates URL-friendly slugs for all products,
 * useful after bulk imports or data migrations where slugs might be missing.
 *
 * Slug Format:
 * - Combines product name + ID for uniqueness
 * - Example: "Neverfull MM" with ID 123 → "neverfull-mm-123"
 *
 * Usage:
 *   php artisan products:backfill-slugs
 *
 * Note: This command updates ALL products, not just those missing slugs.
 * It processes in chunks of 200 to manage memory for large catalogs.
 *
 * @see \App\Console\Commands\ImportLV For slug generation during import
 */
class BackfillProductSlugs extends Command
{
    protected $signature = 'products:backfill-slugs';

    protected $description = 'Generate slugs for all products that are missing slugs';

    /**
     * Execute the backfill operation.
     *
     * Processes products in chunks to avoid memory issues with large datasets.
     *
     * @return int Command::SUCCESS
     */
    public function handle(): int
    {
        $this->info("Starting slug backfill...");

        Product::chunk(200, function ($products) {
            foreach ($products as $p) {
                // Generate unique slug using name + ID
                $slug = Str::slug($p->name . '-' . $p->id);

                $p->slug = $slug;
                $p->save();

                $this->line("Updated: {$p->name} → {$slug}");
            }
        });

        $this->info("Slug backfill complete!");
        return self::SUCCESS;
    }
}
