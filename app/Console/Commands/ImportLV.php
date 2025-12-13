<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Product;

class ImportLV extends Command
{
    protected $signature = 'import:lv';
    protected $description = 'Import LV product folders into the database';

    public function handle()
    {
        $base = storage_path('app/public/imports');

        if (!is_dir($base)) {
            $this->error("Folder not found: $base");
            return Command::FAILURE;
        }

        // Get all category folders
        $folders = array_filter(scandir($base), function ($f) use ($base) {
            return $f !== '.' && $f !== '..' && is_dir("$base/$f");
        });

        foreach ($folders as $folder) {

            // Expect: lv-{section}-{gender}
            if (!preg_match('/^lv-([a-z]+)-([a-z]+)$/i', $folder, $m)) {
                $this->warn("Skipping invalid folder name: $folder");
                continue;
            }

            $section = strtolower($m[1]);  // e.g., clothes, shoes, bags
            $gender  = strtolower($m[2]);  // men, women

            $categorySlug = "{$gender}-{$section}";
            $path = "$base/$folder";

            $this->info("📂 Category: $categorySlug ($folder)");

            // Scan product folders inside category
            foreach (scandir($path) as $dir) {
                if ($dir === '.' || $dir === '..') continue;

                $productDir = "$path/$dir";
                if (!is_dir($productDir)) continue;

                // Collect images
                $images = array_values(array_filter(scandir($productDir), fn ($f) =>
                    preg_match('/\.(jpg|jpeg|png)$/i', $f)
                ));

                if (empty($images)) {
                    $this->warn("  ⏭  Skipping $dir — No images");
                    continue;
                }

                $firstImage = $images[0];

                Product::create([
                    'name'          => $dir,
                    'slug'          => Str::slug($dir),
                    'gender'        => $gender,
                    'section'       => $section,
                    'category_slug' => $categorySlug,
                    'image_path'    => "imports/$folder/$dir/$firstImage",
                ]);

                $this->info("  ✔ Imported $dir");
            }

            $this->line("");
        }

        $this->info("🎉 Import complete!");
        return Command::SUCCESS;
    }
}
