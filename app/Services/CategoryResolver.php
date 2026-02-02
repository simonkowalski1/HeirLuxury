<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Resolves URL slugs to database category_slug values.
 *
 * This service handles the hierarchical category system where:
 * - Gender slugs (women, men) → all leaf categories for that gender
 * - Section slugs (women-bags) → all leaf categories in that section
 * - Leaf slugs (louis-vuitton-women-bags) → direct database match
 *
 * The category taxonomy is defined in config/categories.php and cached
 * for 24 hours to avoid repeated config parsing.
 *
 * @see config/categories.php For the category taxonomy definition
 */
class CategoryResolver
{
    /** @var int Cache TTL for category map (24 hours) */
    protected const MAP_CACHE_TTL = 86400;

    /**
     * Resolve a URL slug to an array of database category_slug values.
     *
     * @param  string  $slug  URL slug from route parameter
     * @return array{slugs: array<string>, active: array{slug: string, gender: ?string, section: ?string, name: string, type: string}}
     */
    public function resolve(string $slug): array
    {
        $slug = Str::of($slug)->lower()->slug('-')->toString();
        $items = $this->getCategoryMap();

        // Default active category info
        $active = [
            'slug' => $slug,
            'gender' => null,
            'section' => null,
            'name' => Str::headline(str_replace('-', ' ', $slug)),
            'type' => 'leaf',
        ];

        $slugsForQuery = [];

        if (isset($items[$slug])) {
            $active = $items[$slug];

            // Gender slug (women/men) → all leaf categories for that gender
            if (in_array($slug, ['women', 'men'], true)) {
                foreach ($items as $item) {
                    if (($item['type'] ?? null) !== 'leaf') {
                        continue;
                    }
                    if (($item['gender'] ?? null) !== $slug) {
                        continue;
                    }
                    $slugsForQuery[] = $item['slug'];
                }
            }
            // Section slug (women-bags) → all leaf categories in that section
            elseif (($active['type'] ?? null) === 'group' && $active['section']) {
                foreach ($items as $item) {
                    if (($item['type'] ?? null) !== 'leaf') {
                        continue;
                    }
                    if (($item['gender'] ?? null) !== $active['gender']) {
                        continue;
                    }
                    if (($item['section'] ?? null) !== $active['section']) {
                        continue;
                    }
                    $slugsForQuery[] = $item['slug'];
                }
            }
            // Leaf slug → direct match
            else {
                $slugsForQuery[] = $slug;
            }
        } else {
            // Unknown slug: treat as leaf (may match a product category_slug directly)
            $slugsForQuery[] = $slug;
        }

        return [
            'slugs' => array_values(array_unique(array_filter($slugsForQuery))),
            'active' => $active,
        ];
    }

    /**
     * Build and cache the category map from config.
     *
     * @return array<string, array{slug: string, gender: string, section: ?string, name: string, type: string}>
     */
    public function getCategoryMap(): array
    {
        return Cache::remember('category_map', self::MAP_CACHE_TTL, function () {
            $catalog = (array) config('categories', []);
            $items = [];

            $fromHref = fn ($href) => trim(str_replace('/categories/', '', (string) $href), '/');

            foreach (['women', 'men'] as $gender) {
                if (! isset($catalog[$gender]) || ! is_array($catalog[$gender])) {
                    continue;
                }

                foreach ($catalog[$gender] as $section => $links) {
                    if (! is_array($links)) {
                        continue;
                    }

                    $sectionSlug = Str::of($section)->lower()->slug('-')->toString();

                    // Synthetic section slug: women-bags, men-shoes, etc.
                    $items["{$gender}-{$sectionSlug}"] = [
                        'slug' => "{$gender}-{$sectionSlug}",
                        'gender' => $gender,
                        'section' => $section,
                        'name' => 'All '.ucfirst($gender).' '.$section,
                        'type' => 'group',
                    ];

                    foreach ($links as $link) {
                        $rawSlug = $link['params']['category']
                            ?? (isset($link['href']) ? $fromHref($link['href']) : null);
                        if (! $rawSlug) {
                            continue;
                        }

                        $leafSlug = Str::of($rawSlug)->lower()->slug('-')->toString();

                        // Leaf slug: louis-vuitton-women-bags (matches products.category_slug)
                        $items[$leafSlug] = [
                            'slug' => $leafSlug,
                            'gender' => $gender,
                            'section' => $section,
                            'name' => $link['name'] ?? Str::headline($leafSlug),
                            'type' => 'leaf',
                        ];
                    }
                }

                // Synthetic gender slug: women, men
                $items[$gender] = [
                    'slug' => $gender,
                    'gender' => $gender,
                    'section' => null,
                    'name' => 'All '.ucfirst($gender),
                    'type' => 'group',
                ];
            }

            return $items;
        });
    }

    /**
     * Get the full catalog config (for navigation rendering).
     */
    public function getCatalog(): array
    {
        return Cache::remember('catalog.config', self::MAP_CACHE_TTL, fn () => (array) config('categories', []));
    }

    /**
     * Generate a hash for cache keys from slug array.
     *
     * @param  array<string>  $slugs
     */
    public function hashSlugs(array $slugs): string
    {
        if (empty($slugs)) {
            return 'all';
        }
        sort($slugs);

        return md5(implode(',', $slugs));
    }
}
