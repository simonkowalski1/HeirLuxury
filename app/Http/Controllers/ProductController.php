<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function show(string $categorySlug, string $productSlug)
    {
        // 1. Find the product that matches BOTH category + slug
        $product = Product::where('category_slug', $categorySlug)
            ->where('slug', $productSlug)
            ->firstOrFail();

        // 2. Map category_slug -> base imports folder
        $baseFolder = match ($product->category_slug) {
            'louis-vuitton-women-bags'  => 'lv-bags-women',
            'louis-vuitton-women-shoes' => 'lv-shoes-women',
            'lv-women-clothes'          => 'lv-clothes-women',
            'lv-men-clothes'            => 'lv-clothes-men',
            'lv-men-shoes'              => 'lv-shoes-men',
            default                     => null,
        };

        $images = [];

        // 3. Build gallery from STORAGE if we know the folder
        if ($baseFolder && $product->folder) {
            $disk = Storage::disk('public'); // <-- IMPORTANT
            $dir  = "imports/{$baseFolder}/{$product->folder}";

            if ($disk->exists($dir)) {
                $files = collect($disk->allFiles($dir))
                    ->filter(fn ($path) => preg_match('/\.(jpe?g|png|webp)$/i', $path))
                    ->sort()
                    ->values();

                $images = $files->map(function (string $path) use ($disk, $product) {
                    return [
                        'src' => $disk->url($path),
                        'alt' => $product->name,
                    ];
                })->all();
            }
        }

        // 4. Fallback to the single image_path, if folder-based gallery is empty
        if (empty($images) && $product->image_path) {
            $images[] = [
                'src' => Storage::disk('public')->url($product->image_path),
                'alt' => $product->name,
            ];
        }

        // 5. Related products from same category
        $related = Product::where('category_slug', $product->category_slug)
            ->where('id', '!=', $product->id)
            ->latest('id')
            ->take(12)
            ->get();

        // 6. Breadcrumbs
        $categoryLabel = Str::headline(str_replace('-', ' ', $product->category_slug));

        $breadcrumbs = [
            ['label' => 'Home',    'href' => route('home')],
            ['label' => 'Catalog', 'href' => route('catalog.grouped')],
            [
                'label' => $categoryLabel,
                'href'  => route('categories.show', ['category' => $product->category_slug]),
            ],
            ['label' => $product->name, 'href' => null],
        ];

        return view('catalog.product', [
            'product'     => $product,
            'images'      => $images,
            'related'     => $related,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
