<?php

namespace App\Console\Commands;

use App\Services\ThumbnailService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Product;

/**
 * Import Louis Vuitton product catalogs from folder structure into the database.
 *
 * This command scans the storage/app/public/imports directory for product folders
 * and creates/updates Product records in the database. It's designed for bulk
 * importing luxury product catalogs that have been organized into folders.
 *
 * Expected Folder Structure:
 * ```
 * storage/app/public/imports/
 * ├── lv-bags-women/
 * │   ├── Neverfull MM/
 * │   │   ├── 0000.jpg
 * │   │   ├── 0001.jpg
 * │   │   └── ...
 * │   └── Speedy 25/
 * │       └── ...
 * ├── lv-shoes-women/
 * │   └── ...
 * └── lv-clothes-men/
 *     └── ...
 * ```
 *
 * Usage:
 *   php artisan import:lv              # Import new products, skip existing
 *   php artisan import:lv --fresh      # Clear all products first, then import
 *   php artisan import:lv --skip-thumbnails  # Import without generating thumbnails
 *
 * @see \App\Services\ThumbnailService For thumbnail generation during import
 * @see \App\Console\Commands\GenerateThumbnails For batch thumbnail generation
 */
class ImportLV extends Command
{
    protected $signature = 'import:lv
                            {--fresh : Delete existing products first}
                            {--skip-thumbnails : Skip thumbnail generation}';

    protected $description = 'Import LV product folders into the database';

    /**
     * Execute the import process.
     *
     * The import process:
     * 1. Scans the imports directory for category folders (lv-bags-women, etc.)
     * 2. Maps each folder to a category_slug (louis-vuitton-women-bags, etc.)
     * 3. For each product subfolder, creates/updates a Product record
     * 4. Optionally generates thumbnails for all product images
     *
     * @param ThumbnailService $thumbnailService Injected service for thumbnail generation
     * @return int Command::SUCCESS or Command::FAILURE
     */
    public function handle(ThumbnailService $thumbnailService)
    {
        $base = storage_path('app/public/imports');

        if (!is_dir($base)) {
            $this->error("Folder not found: $base");
            return Command::FAILURE;
        }

        // Clear all products if --fresh flag is provided
        if ($this->option('fresh')) {
            Product::truncate();
            $this->warn("Cleared all existing products.");
        }

        /*
         * Folder-to-category mapping.
         *
         * This maps the physical folder names in storage to the category_slug
         * values used in the database and URLs. The category_slug follows the
         * pattern: {brand}-{gender}-{section}
         *
         * These slugs must match the entries in config/categories.php for
         * proper navigation integration.
         */
        $folderToCategorySlug = [
            'lv-bags-women'    => 'louis-vuitton-women-bags',
            'lv-shoes-women'   => 'louis-vuitton-women-shoes',
            'lv-clothes-women' => 'louis-vuitton-women-clothes',
            'lv-bags-men'      => 'louis-vuitton-men-bags',
            'lv-shoes-men'     => 'louis-vuitton-men-shoes',
            'lv-clothes-men'   => 'louis-vuitton-men-clothes',
        ];

        // Get all category folders (excluding . and ..)
        $folders = array_filter(scandir($base), function ($f) use ($base) {
            return $f !== '.' && $f !== '..' && is_dir("$base/$f");
        });

        foreach ($folders as $folder) {
            // Skip folders we don't have a mapping for
            if (!isset($folderToCategorySlug[$folder])) {
                $this->warn("Skipping unknown folder: $folder");
                continue;
            }

            $categorySlug = $folderToCategorySlug[$folder];

            /*
             * Extract gender and section from the category slug.
             * Example: "louis-vuitton-women-bags" -> gender="women", section="bags"
             *
             * These values are stored on the Product model for easier filtering
             * and are used by the CategoryController for navigation.
             */
            preg_match('/louis-vuitton-(women|men)-(.+)/', $categorySlug, $matches);
            $gender  = $matches[1] ?? 'women';
            $section = $matches[2] ?? 'bags';

            $path = "$base/$folder";

            $this->info("📂 Importing: $folder → $categorySlug");

            // Process each product subfolder within the category
            foreach (scandir($path) as $dir) {
                if ($dir === '.' || $dir === '..') continue;

                $productDir = "$path/$dir";
                if (!is_dir($productDir)) continue;

                // Collect all image files (jpg, jpeg, png, webp)
                $images = array_values(array_filter(scandir($productDir), fn ($f) =>
                    preg_match('/\.(jpg|jpeg|png|webp)$/i', $f)
                ));

                // Skip folders with no images
                if (empty($images)) {
                    $this->warn("  ⭕ Skipping $dir — No images");
                    continue;
                }

                // Sort to ensure consistent first image (typically 0000.jpg)
                sort($images);
                $firstImage = $images[0];

                // Build the relative path for the primary image
                $imagePath = "imports/$folder/$dir/$firstImage";

                /*
                 * Create or update the product record.
                 *
                 * Uses category_slug + name as the unique key to prevent duplicates
                 * while allowing the same product name in different categories.
                 */
                Product::updateOrCreate(
                    [
                        'category_slug' => $categorySlug,
                        'name'          => $dir,
                    ],
                    [
                        'slug'       => Str::slug($dir),
                        'folder'     => $dir,
                        'gender'     => $gender,
                        'section'    => $section,
                        'image'      => $firstImage,
                        'image_path' => $imagePath,
                    ]
                );

                // Generate optimized thumbnails for all images unless skipped
                if (!$this->option('skip-thumbnails')) {
                    foreach ($images as $img) {
                        $imgPath = "imports/$folder/$dir/$img";
                        $thumbnailService->generateAll($imgPath);
                    }
                }

                $this->info("  ✔ Imported $dir");
            }

            $this->line("");
        }

        $this->info("🎉 Import complete!");
        return Command::SUCCESS;
    }
}
