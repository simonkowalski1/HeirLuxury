<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Product;

class ImportLV extends Command
{
    protected $signature = 'import:lv {--fresh : Delete existing products first}';
    protected $description = 'Import LV product folders into the database';

    public function handle()
    {
        $base = storage_path('app/public/imports');

        if (!is_dir($base)) {
            $this->error("Folder not found: $base");
            return Command::FAILURE;
        }

        // Optional: clear existing products
        if ($this->option('fresh')) {
            Product::truncate();
            $this->warn("Cleared all existing products.");
        }

        // Map folder names to category slugs
        $folderToCategorySlug = [
            'lv-bags-women'    => 'louis-vuitton-women-bags',
            'lv-shoes-women'   => 'louis-vuitton-women-shoes',
            'lv-clothes-women' => 'louis-vuitton-women-clothes',
            'lv-bags-men'      => 'louis-vuitton-men-bags',
            'lv-shoes-men'     => 'louis-vuitton-men-shoes',
            'lv-clothes-men'   => 'louis-vuitton-men-clothes',
        ];

        // Get all category folders
        $folders = array_filter(scandir($base), function ($f) use ($base) {
            return $f !== '.' && $f !== '..' && is_dir("$base/$f");
        });

        foreach ($folders as $folder) {
            // Check if we have a mapping for this folder
            if (!isset($folderToCategorySlug[$folder])) {
                $this->warn("Skipping unknown folder: $folder");
                continue;
            }

            $categorySlug = $folderToCategorySlug[$folder];
            
            // Extract gender and section from category slug
            // e.g., louis-vuitton-women-bags -> gender=women, section=bags
            preg_match('/louis-vuitton-(women|men)-(.+)/', $categorySlug, $matches);
            $gender  = $matches[1] ?? 'women';
            $section = $matches[2] ?? 'bags';

            $path = "$base/$folder";

            $this->info("📂 Importing: $folder → $categorySlug");

            // Scan product folders inside category
            foreach (scandir($path) as $dir) {
                if ($dir === '.' || $dir === '..') continue;

                $productDir = "$path/$dir";
                if (!is_dir($productDir)) continue;

                // Collect images
                $images = array_values(array_filter(scandir($productDir), fn ($f) =>
                    preg_match('/\.(jpg|jpeg|png|webp)$/i', $f)
                ));

                if (empty($images)) {
                    $this->warn("  ⭕ Skipping $dir — No images");
                    continue;
                }

                sort($images); // Ensure first image is 0000.jpg or similar
                $firstImage = $images[0];

                Product::updateOrCreate(
                    [
                        'category_slug' => $categorySlug,
                        'name'          => $dir,
                    ],
                    [
                        'slug'       => Str::slug($dir),
                        'folder'     => $dir,  // ← THIS WAS MISSING!
                        'gender'     => $gender,
                        'section'    => $section,
                        'image'      => $firstImage,
                        'image_path' => "imports/$folder/$dir/$firstImage",
                    ]
                );

                $this->info("  ✔ Imported $dir");
            }

            $this->line("");
        }

        $this->info("🎉 Import complete!");
        return Command::SUCCESS;
    }
}