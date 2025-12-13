<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;

class CategoryController extends Controller
{
    public function index()
    {
        $products = Product::query()
        ->orderBy('id', 'asc')   // LV 0000 → LV 0998
        ->paginate(24);

        return view('categories.index', [
            'title'    => 'All Categories',
            'products' => $products,
        ]);
    }

    public function show(Request $request, string $slug)
{
    $slug     = Str::of($slug)->lower()->slug('-')->toString();
    $catalog  = (array) config('categories', []);
    $fromHref = fn ($href) => trim(str_replace('/categories/', '', (string) $href), '/');

    // Build map:
    // - leaf slugs: the real category_slug values in your products table
    // - synthetic slugs: women, men, women-bags, women-shoes, etc (UI groupings)
    $items = [];
    foreach (['women', 'men'] as $gender) {
        if (!isset($catalog[$gender]) || !is_array($catalog[$gender])) continue;

        foreach ($catalog[$gender] as $section => $links) {
            if (!is_array($links)) continue;

            $sectionSlug = Str::of($section)->lower()->slug('-')->toString();

            // synthetic: women-bags, women-shoes...
            $items["{$gender}-{$sectionSlug}"] = [
                'slug'    => "{$gender}-{$sectionSlug}",
                'gender'  => $gender,
                'section' => $section,
                'name'    => "All " . ucfirst($gender) . " " . $section,
                'type'    => 'group',
            ];

            foreach ($links as $item) {
                $rawSlug = $item['params']['slug']
                    ?? (isset($item['href']) ? $fromHref($item['href']) : null);
                if (!$rawSlug) continue;

                $leafSlug = Str::of($rawSlug)->lower()->slug('-')->toString();

                // leaf: louis-vuitton-women-bags (this should match products.category_slug)
                $items[$leafSlug] = [
                    'slug'    => $leafSlug,
                    'gender'  => $gender,
                    'section' => $section,
                    'name'    => $item['name'] ?? Str::headline($leafSlug),
                    'type'    => 'leaf',
                ];
            }
        }

        // synthetic: women / men
        $items[$gender] = [
            'slug'    => $gender,
            'gender'  => $gender,
            'section' => null,
            'name'    => "All " . ucfirst($gender),
            'type'    => 'group',
        ];
    }

    // Default active
    $active = [
        'slug'    => $slug,
        'gender'  => null,
        'section' => null,
        'name'    => Str::headline(str_replace('-', ' ', $slug)),
        'type'    => 'leaf',
    ];

    $slugsForQuery = [];

    // If slug matches something we know
    if (isset($items[$slug])) {
        $active = $items[$slug];

        // women / men => all LEAF slugs under that gender
        if (in_array($slug, ['women', 'men'], true)) {
            foreach ($items as $it) {
                if (($it['type'] ?? null) !== 'leaf') continue;
                if (($it['gender'] ?? null) !== $slug) continue;
                $slugsForQuery[] = $it['slug'];
            }
        }
        // women-bags / men-shoes => all LEAF slugs in that gender+section
        elseif (($active['type'] ?? null) === 'group' && $active['section']) {
            foreach ($items as $it) {
                if (($it['type'] ?? null) !== 'leaf') continue;
                if (($it['gender'] ?? null) !== $active['gender']) continue;
                if (($it['section'] ?? null) !== $active['section']) continue;
                $slugsForQuery[] = $it['slug'];
            }
        }
        // leaf => just itself
        else {
            $slugsForQuery[] = $slug;
        }
    }
    // Unknown slug: treat as leaf
    else {
        $slugsForQuery[] = $slug;
    }

    // normalize
    $slugsForQuery = array_values(array_unique(array_filter($slugsForQuery)));

    $products = Product::query()
        ->when(count($slugsForQuery) > 0, fn ($q) => $q->whereIn('category_slug', $slugsForQuery))
        ->orderBy('id', 'asc')
        ->paginate(24);

    return view('categories.show', [
        'slug'     => $slug,
        'title'    => $active['name'],
        'active'   => $active,
        'catalog'  => $catalog,
        'products' => $products,
    ]);
}

}

