<?php

namespace App\Console\Commands;

use App\Services\ThumbnailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateThumbnails extends Command
{
    protected $signature = 'thumbnails:generate
                            {--size=all : Size to generate (card, gallery, thumb, or all)}
                            {--folder= : Specific import folder to process}
                            {--force : Regenerate existing thumbnails}';

    protected $description = 'Generate optimized thumbnails for product images';

    public function handle(ThumbnailService $thumbnailService): int
    {
        $size = $this->option('size');
        $folder = $this->option('folder');
        $force = $this->option('force');

        $storage = Storage::disk('public');
        $basePath = 'imports';

        // Determine which folders to process
        if ($folder) {
            $folders = [$folder];
        } else {
            $folders = collect($storage->directories($basePath))
                ->map(fn($path) => basename($path))
                ->filter()
                ->values()
                ->all();
        }

        if (empty($folders)) {
            $this->error('No import folders found.');
            return Command::FAILURE;
        }

        $this->info("Processing folders: " . implode(', ', $folders));
        $this->newLine();

        $sizes = $size === 'all' ? array_keys(ThumbnailService::SIZES) : [$size];
        $totalProcessed = 0;
        $totalSkipped = 0;
        $totalFailed = 0;

        foreach ($folders as $folderName) {
            $folderPath = "{$basePath}/{$folderName}";

            if (!$storage->exists($folderPath)) {
                $this->warn("Folder not found: {$folderPath}");
                continue;
            }

            $this->info("Processing: {$folderName}");

            // Get all product directories
            $productDirs = $storage->directories($folderPath);

            $progressBar = $this->output->createProgressBar(count($productDirs));
            $progressBar->start();

            foreach ($productDirs as $productDir) {
                // Get all images in this product directory
                $files = collect($storage->allFiles($productDir))
                    ->filter(fn($path) => preg_match('/\.(jpe?g|png|webp)$/i', $path))
                    ->values();

                foreach ($files as $imagePath) {
                    foreach ($sizes as $sizeKey) {
                        // Skip if exists and not forcing
                        if (!$force && $thumbnailService->exists($imagePath, $sizeKey)) {
                            $totalSkipped++;
                            continue;
                        }

                        $result = $thumbnailService->generate($imagePath, $sizeKey);

                        if ($result) {
                            $totalProcessed++;
                        } else {
                            $totalFailed++;
                        }
                    }
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);
        }

        $this->info("Thumbnail generation complete!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Generated', $totalProcessed],
                ['Skipped (existing)', $totalSkipped],
                ['Failed', $totalFailed],
            ]
        );

        return Command::SUCCESS;
    }
}
