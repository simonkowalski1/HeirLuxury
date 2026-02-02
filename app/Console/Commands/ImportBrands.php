<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportBrands extends Command
{
    protected $signature = 'import:brands 
                            {--fresh : Delete existing products first}
                            {--skip-thumbnails : Skip thumbnail generation}';

    protected $description = 'Import products from all brand folders';

    public function handle()
    {
        $base = storage_path('app/public/imports');

        if (! is_dir($base)) {
            $this->error("Folder not found: $base");

            return Command::FAILURE;
        }

        if ($this->option('fresh')) {
            Product::truncate();
            $this->warn('Cleared all existing products.');
        }

        /*
         * Folder-to-category mapping.
         * Format: folder-name => [category_slug, brand, gender, section]
         */
        $folderMappings = [
            // Louis Vuitton
            'lv-bags-women' => ['louis-vuitton-women-bags', 'Louis Vuitton', 'women', 'bags'],
            'lv-shoes-women' => ['louis-vuitton-women-shoes', 'Louis Vuitton', 'women', 'shoes'],
            'lv-clothes-women' => ['louis-vuitton-women-clothes', 'Louis Vuitton', 'women', 'clothes'],
            'lv-bags-men' => ['louis-vuitton-men-bags', 'Louis Vuitton', 'men', 'bags'],
            'lv-shoes-men' => ['louis-vuitton-men-shoes', 'Louis Vuitton', 'men', 'shoes'],
            'lv-clothes-men' => ['louis-vuitton-men-clothes', 'Louis Vuitton', 'men', 'clothes'],
            'lv-belts-women' => ['louis-vuitton-women-belts', 'Louis Vuitton', 'women', 'belts'],
            'lv-glasses-women' => ['louis-vuitton-women-glasses', 'Louis Vuitton', 'women', 'glasses'],
            'lv-jewelry-women' => ['louis-vuitton-women-jewelry', 'Louis Vuitton', 'women', 'jewelry'],

            // Chanel
            'chanel-bags-women' => ['chanel-women-bags', 'Chanel', 'women', 'bags'],
            'chanel-shoes-women' => ['chanel-women-shoes', 'Chanel', 'women', 'shoes'],
            'chanel-clothes-women' => ['chanel-women-clothes', 'Chanel', 'women', 'clothes'],
            'chanel-shoes-men' => ['chanel-men-shoes', 'Chanel', 'men', 'shoes'],
            'chanel-clothes-men' => ['chanel-men-clothes', 'Chanel', 'men', 'clothes'],
            'chanel-belts-women' => ['chanel-women-belts', 'Chanel', 'women', 'belts'],
            'chanel-glasses-women' => ['chanel-women-glasses', 'Chanel', 'women', 'glasses'],
            'chanel-jewelry-women' => ['chanel-women-jewelry', 'Chanel', 'women', 'jewelry'],

            // Dior
            'dior-bags-women' => ['dior-women-bags', 'Dior', 'women', 'bags'],
            'dior-shoes-women' => ['dior-women-shoes', 'Dior', 'women', 'shoes'],
            'dior-clothes-women' => ['dior-women-clothes', 'Dior', 'women', 'clothes'],
            'dior-shoes-men' => ['dior-men-shoes', 'Dior', 'men', 'shoes'],
            'dior-clothes-men' => ['dior-men-clothes', 'Dior', 'men', 'clothes'],

            // Hermès
            'hermes-bags-women' => ['hermes-women-bags', 'Hermès', 'women', 'bags'],
            'hermes-shoes-women' => ['hermes-women-shoes', 'Hermès', 'women', 'shoes'],
            'hermes-clothes-women' => ['hermes-women-clothes', 'Hermès', 'women', 'clothes'],
            'hermes-shoes-men' => ['hermes-men-shoes', 'Hermès', 'men', 'shoes'],
            'hermes-clothes-men' => ['hermes-men-clothes', 'Hermès', 'men', 'clothes'],
            'hermes-belts-women' => ['hermes-women-belts', 'Hermès', 'women', 'belts'],
            'hermes-glasses-women' => ['hermes-women-glasses', 'Hermès', 'women', 'glasses'],
            'hermes-jewelry-women' => ['hermes-women-jewelry', 'Hermès', 'women', 'jewelry'],
        ];

        $folders = array_filter(scandir($base), function ($f) use ($base) {
            return $f !== '.' && $f !== '..' && is_dir("$base/$f");
        });

        $totalProducts = 0;

        foreach ($folders as $folder) {
            if (! isset($folderMappings[$folder])) {
                $this->warn("⚠️  Skipping unknown folder: $folder");

                continue;
            }

            [$categorySlug, $brand, $gender, $section] = $folderMappings[$folder];

            $path = "$base/$folder";
            $this->info("\n📂 Importing: $folder → $categorySlug");

            $productFolders = array_filter(scandir($path), function ($f) use ($path) {
                return $f !== '.' && $f !== '..' && is_dir("$path/$f");
            });

            $count = 0;

            foreach ($productFolders as $productFolder) {
                $productPath = "$path/$productFolder";

                $images = array_values(array_filter(scandir($productPath), fn ($f) => preg_match('/\.(jpg|jpeg|png|webp)$/i', $f)
                ));

                if (empty($images)) {
                    continue;
                }

                // Sort by file size descending to select largest (best quality) image as thumbnail
                usort($images, function ($a, $b) use ($productPath) {
                    return filesize("$productPath/$b") <=> filesize("$productPath/$a");
                });
                $firstImage = $images[0];
                $imagePath = "imports/$folder/$productFolder/$firstImage";

                // Unique slug: section-productname
                $uniqueSlug = $section.'-'.Str::slug($productFolder);

                Product::updateOrCreate(
                    [
                        'category_slug' => $categorySlug,
                        'name' => $productFolder,
                    ],
                    [
                        'slug' => $uniqueSlug,
                        'folder' => $productFolder,
                        'gender' => $gender,
                        'section' => $section,
                        'brand' => $brand,
                        'image' => $firstImage,
                        'image_path' => $imagePath,
                    ]
                );

                $count++;
            }

            $this->info("   ✔ Imported $count products");
            $totalProducts += $count;
        }

        $this->info("\n🎉 Import complete! Total: $totalProducts products");

        $this->info("\n📊 Category Summary:");
        $categories = Product::select('category_slug', \DB::raw('count(*) as total'))
            ->groupBy('category_slug')
            ->orderBy('category_slug')
            ->get();

        foreach ($categories as $cat) {
            $this->line("   {$cat->category_slug}: {$cat->total}");
        }

        return Command::SUCCESS;
    }
}
