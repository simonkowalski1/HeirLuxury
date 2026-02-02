<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeduplicateImages extends Command
{
    protected $signature = 'images:deduplicate
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--min-size=150000 : Minimum file size in bytes to keep (default 150KB)}
                            {--path= : Specific subfolder to process (e.g., chanel-bags-women)}';

    protected $description = 'Remove duplicate lower-quality images from product folders';

    public function handle()
    {
        $base = storage_path('app/public/imports');
        $dryRun = $this->option('dry-run');
        $minSize = (int) $this->option('min-size');
        $specificPath = $this->option('path');

        if (! is_dir($base)) {
            $this->error("Imports folder not found: $base");

            return Command::FAILURE;
        }

        $this->info($dryRun ? '🔍 DRY RUN MODE - No files will be deleted' : '🗑️  DELETE MODE - Files will be permanently removed');
        $this->info('Minimum file size threshold: '.number_format($minSize / 1024, 1).' KB');
        $this->newLine();

        $totalDeleted = 0;
        $totalSpaceSaved = 0;
        $totalKept = 0;
        $deletedFiles = [];

        // Get brand folders to process
        $brandFolders = $this->getBrandFolders($base, $specificPath);

        foreach ($brandFolders as $brandFolder) {
            $brandPath = "$base/$brandFolder";
            $this->info("📂 Processing: $brandFolder");

            $productFolders = $this->getProductFolders($brandPath);
            $brandDeleted = 0;
            $brandKept = 0;

            foreach ($productFolders as $productFolder) {
                $productPath = "$brandPath/$productFolder";
                $result = $this->processProductFolder($productPath, $minSize, $dryRun);

                $brandDeleted += $result['deleted'];
                $brandKept += $result['kept'];
                $totalSpaceSaved += $result['space_saved'];

                foreach ($result['deleted_files'] as $file) {
                    $deletedFiles[] = $file;
                }
            }

            $totalDeleted += $brandDeleted;
            $totalKept += $brandKept;

            $this->line("   ✔ Kept: $brandKept | ".($dryRun ? 'Would delete' : 'Deleted').": $brandDeleted");
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════');
        $this->info($dryRun ? '📋 DRY RUN SUMMARY' : '📋 DELETION SUMMARY');
        $this->info('═══════════════════════════════════════════════════════');
        $this->line("   Images kept:    $totalKept");
        $this->line('   Images '.($dryRun ? 'to delete' : 'deleted').": $totalDeleted");
        $this->line('   Space '.($dryRun ? 'to save' : 'saved').':   '.$this->formatBytes($totalSpaceSaved));
        $this->newLine();

        // Show sample of files to be deleted in dry-run mode
        if ($dryRun && count($deletedFiles) > 0) {
            $this->info('Sample of files that would be deleted (first 20):');
            $this->newLine();

            foreach (array_slice($deletedFiles, 0, 20) as $file) {
                $this->line('   🗑️  '.$file['path'].' ('.$this->formatBytes($file['size']).')');
            }

            if (count($deletedFiles) > 20) {
                $this->line('   ... and '.(count($deletedFiles) - 20).' more files');
            }

            $this->newLine();
            $this->warn('Run without --dry-run to actually delete these files.');
        }

        return Command::SUCCESS;
    }

    protected function getBrandFolders(string $base, ?string $specificPath): array
    {
        if ($specificPath) {
            $fullPath = "$base/$specificPath";
            if (! is_dir($fullPath)) {
                $this->error("Specified path not found: $specificPath");

                return [];
            }

            return [$specificPath];
        }

        return array_values(array_filter(scandir($base), function ($f) use ($base) {
            return $f !== '.' && $f !== '..' && is_dir("$base/$f");
        }));
    }

    protected function getProductFolders(string $brandPath): array
    {
        return array_values(array_filter(scandir($brandPath), function ($f) use ($brandPath) {
            return $f !== '.' && $f !== '..' && is_dir("$brandPath/$f");
        }));
    }

    protected function processProductFolder(string $productPath, int $minSize, bool $dryRun): array
    {
        $result = [
            'deleted' => 0,
            'kept' => 0,
            'space_saved' => 0,
            'deleted_files' => [],
        ];

        $files = array_filter(scandir($productPath), function ($f) use ($productPath) {
            return $f !== '.' && $f !== '..' &&
                   is_file("$productPath/$f") &&
                   preg_match('/\.(jpg|jpeg|png|webp)$/i', $f);
        });

        foreach ($files as $file) {
            $filePath = "$productPath/$file";
            $fileSize = filesize($filePath);

            if ($fileSize < $minSize) {
                $result['deleted']++;
                $result['space_saved'] += $fileSize;
                $result['deleted_files'][] = [
                    'path' => str_replace(storage_path('app/public/imports/'), '', $filePath),
                    'size' => $fileSize,
                ];

                if (! $dryRun) {
                    unlink($filePath);
                }
            } else {
                $result['kept']++;
            }
        }

        return $result;
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }
}
