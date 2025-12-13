{{-- resources/views/components/product/card.blade.php --}}
@props(['product' => null, 'item' => null, 'p' => null])

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Route;

    $prod = $product ?? $item ?? $p;
    if (! $prod) {
        return;
    }

    $val = fn($key, $default = null) => data_get($prod, $key, $default);

    $slug         = $val('slug') ?: Str::slug($val('name', 'product'));
    $categorySlug = $val('category_slug');
    $name         = (string) $val('name', 'Untitled');
    $catName      = $val('category.name')
                    ?: ($categorySlug ? Str::headline(str_replace('-', ' ', $categorySlug)) : null);
    $coming       = $val('status') === 'coming_soon';

    // === Correct href for product page ===
    if ($categorySlug && $slug && Route::has('product.show')) {
        $href = route('product.show', [
            'categorySlug' => $categorySlug,
            'productSlug'  => $slug,
        ]);
    } else {
        $href = '#';
    }

    // === Thumbnail image resolution ===

    // Map category -> base import folder (for when image_path is empty)
    $baseFolder = match ($categorySlug) {
        'louis-vuitton-women-bags'  => 'lv-bags-women',
        'louis-vuitton-women-shoes' => 'lv-shoes-women',
        'lv-women-clothes'          => 'lv-clothes-women',
        'lv-men-clothes'            => 'lv-clothes-men',
        'lv-men-shoes'              => 'lv-shoes-men',
        default                     => null,
    };

    $folder    = $val('folder');      // e.g. "LV 0001"
    $imageName = $val('image');      // e.g. "0000.jpg"

    // 1) Prefer stored image_path (already like "imports/…")
    $imagePath = $val('image_path');

    // 2) If empty, build from baseFolder + folder + image
    if (! $imagePath && $baseFolder && $folder) {
        $filename  = $imageName ?: '0000.jpg';
        // IMPORTANT: no "public/" prefix – Storage::url() already maps to /storage
        $imagePath = "imports/{$baseFolder}/{$folder}/{$filename}";
    }

    if ($imagePath) {
        $img = Storage::url($imagePath);   // -> /storage/imports/...
    } else {
        $img = asset('assets/placeholders/product-dark.png');
    }

    $alt = $val('alt', $name);
@endphp

<a href="{{ $href }}"
   class="group block rounded-3xl bg-[#050814] border border-white/5 overflow-hidden
          shadow-lg hover:border-amber-400/80 hover:shadow-amber-400/30 transition duration-300">
    {{-- Media --}}
    <div class="aspect-[4/3] bg-black/40 relative overflow-hidden">
        <img
            src="{{ $img }}"
            alt="{{ $alt }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
        />

        @if ($coming)
            <span class="absolute top-3 left-3 z-10 inline-flex items-center rounded-full
                         bg-amber-300 text-black text-[11px] font-semibold px-3 py-1">
                Coming Soon
            </span>
        @endif
    </div>

    {{-- Text --}}
    <div class="px-5 py-4 flex flex-col gap-1">
        <div class="flex items-center justify-between gap-2">
            <p class="text-sm font-semibold text-white tracking-wide">
                {{ $name }}
            </p>

            <span class="inline-flex items-center justify-center rounded-full border border-white/15
                         w-7 h-7 text-[11px] text-white/70
                         group-hover:border-amber-400 group-hover:text-amber-300 transition">
                <span class="sr-only">View details</span>
                ›
            </span>
        </div>

        @if ($catName)
            <p class="text-xs text-white/50">
                {{ $catName }}
            </p>
        @endif

        <p class="mt-2 text-[11px] text-amber-300/80 group-hover:text-amber-200 flex items-center gap-1">
            <span>View details</span>
            <span aria-hidden="true">›</span>
        </p>
    </div>
</a>
