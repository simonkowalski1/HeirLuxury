<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\WebpEncoder;

class ThumbnailService
{
    /**
     * Thumbnail sizes configuration
     * - card: Product cards in catalog grid (400x300)
     * - gallery: Main gallery hero image (800x800)
     * - thumb: Gallery thumbnail strip (96x96)
     */
    public const SIZES = [
        'card' => ['width' => 400, 'height' => 300, 'quality' => 80],
        'gallery' => ['width' => 800, 'height' => 800, 'quality' => 85],
        'thumb' => ['width' => 96, 'height' => 96, 'quality' => 75],
    ];

    protected string $disk = 'public';
    protected string $thumbnailDir = 'thumbnails';

    /**
     * Get the thumbnail URL for an image, generating if needed.
     */
    public function getUrl(string $originalPath, string $size = 'card'): ?string
    {
        if (!isset(self::SIZES[$size])) {
            return null;
        }

        $thumbnailPath = $this->getThumbnailPath($originalPath, $size);
        $storage = Storage::disk($this->disk);

        // Return cached/existing thumbnail
        if ($storage->exists($thumbnailPath)) {
            return $storage->url($thumbnailPath);
        }

        // Generate on-demand if original exists
        if ($storage->exists($originalPath)) {
            $this->generate($originalPath, $size);
            return $storage->url($thumbnailPath);
        }

        return null;
    }

    /**
     * Generate a thumbnail for the given image path.
     */
    public function generate(string $originalPath, string $size = 'card'): bool
    {
        if (!isset(self::SIZES[$size])) {
            return false;
        }

        $storage = Storage::disk($this->disk);

        if (!$storage->exists($originalPath)) {
            return false;
        }

        $config = self::SIZES[$size];
        $thumbnailPath = $this->getThumbnailPath($originalPath, $size);

        try {
            // Get full filesystem path for Intervention
            $fullPath = $storage->path($originalPath);

            // Create image instance
            $image = Image::read($fullPath);

            // Resize with cover (crop to fill)
            $image->cover($config['width'], $config['height']);

            // Encode as WebP
            $encoded = $image->encode(new WebpEncoder(quality: $config['quality']));

            // Ensure directory exists and save
            $thumbnailDir = dirname($thumbnailPath);
            if (!$storage->exists($thumbnailDir)) {
                $storage->makeDirectory($thumbnailDir);
            }

            $storage->put($thumbnailPath, (string) $encoded);

            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    /**
     * Generate all thumbnail sizes for an image.
     */
    public function generateAll(string $originalPath): array
    {
        $results = [];

        foreach (array_keys(self::SIZES) as $size) {
            $results[$size] = $this->generate($originalPath, $size);
        }

        return $results;
    }

    /**
     * Get thumbnail path based on original path and size.
     * Example: imports/lv-bags-women/LV 0001/0000.jpg -> thumbnails/card/lv-bags-women/LV 0001/0000.webp
     */
    public function getThumbnailPath(string $originalPath, string $size): string
    {
        // Remove 'imports/' prefix if present
        $relativePath = preg_replace('#^imports/#', '', $originalPath);

        // Change extension to .webp
        $webpPath = preg_replace('/\.(jpe?g|png|webp)$/i', '.webp', $relativePath);

        return "{$this->thumbnailDir}/{$size}/{$webpPath}";
    }

    /**
     * Check if thumbnail exists for given path and size.
     */
    public function exists(string $originalPath, string $size = 'card'): bool
    {
        $thumbnailPath = $this->getThumbnailPath($originalPath, $size);
        return Storage::disk($this->disk)->exists($thumbnailPath);
    }

    /**
     * Get image URLs for product gallery with optimized sizes.
     * Returns array with 'src' (gallery size), 'thumb' (thumbnail), 'original' (full size for lightbox).
     */
    public function getGalleryImages(string $basePath, array $files): array
    {
        $storage = Storage::disk($this->disk);
        $images = [];

        foreach ($files as $file) {
            $originalPath = $basePath . '/' . basename($file);

            $images[] = [
                'src' => $this->getUrl($originalPath, 'gallery') ?? $storage->url($originalPath),
                'thumb' => $this->getUrl($originalPath, 'thumb') ?? $storage->url($originalPath),
                'original' => $storage->url($originalPath),
                'alt' => pathinfo($file, PATHINFO_FILENAME),
            ];
        }

        return $images;
    }

    /**
     * Get card thumbnail URL with fallback to original.
     */
    public function getCardUrl(string $originalPath): string
    {
        $storage = Storage::disk($this->disk);

        return $this->getUrl($originalPath, 'card')
            ?? $storage->url($originalPath);
    }

    /**
     * Clear cached thumbnail path lookups.
     */
    public function clearCache(string $originalPath): void
    {
        foreach (array_keys(self::SIZES) as $size) {
            Cache::forget("thumbnail:{$size}:" . md5($originalPath));
        }
    }
}
