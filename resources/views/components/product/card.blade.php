{{-- resources/views/components/product/card.blade.php --}}
@props(['product' => null, 'item' => null, 'p' => null])

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Route;
    use App\Services\ThumbnailService;

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
            'locale'      => app()->getLocale(),
            'category'    => $categorySlug,
            'productSlug' => $slug,
        ]);
    } else {
        $href = '#';
    }

    // === Thumbnail image resolution ===
    // Map category_slug -> base import folder
    $baseFolder = match ($categorySlug) {
        // Louis Vuitton
        'louis-vuitton-women-bags'    => 'lv-bags-women',
        'louis-vuitton-women-shoes'   => 'lv-shoes-women',
        'louis-vuitton-women-clothes' => 'lv-clothes-women',
        'louis-vuitton-men-bags'      => 'lv-bags-men',
        'louis-vuitton-men-shoes'     => 'lv-shoes-men',
        'louis-vuitton-men-clothes'   => 'lv-clothes-men',
        'louis-vuitton-women-belts'   => 'lv-belts-women',
        'louis-vuitton-women-glasses' => 'lv-glasses-women',
        'louis-vuitton-women-jewelry' => 'lv-jewelry-women',
        
        // Chanel
        'chanel-women-bags'           => 'chanel-bags-women',
        'chanel-women-shoes'          => 'chanel-shoes-women',
        'chanel-women-clothes'        => 'chanel-clothes-women',
        'chanel-men-shoes'            => 'chanel-shoes-men',
        'chanel-men-clothes'          => 'chanel-clothes-men',
        'chanel-women-belts'          => 'chanel-belts-women',
        'chanel-women-glasses'        => 'chanel-glasses-women',
        'chanel-women-jewelry'        => 'chanel-jewelry-women',
        
        // Dior
        'dior-women-bags'             => 'dior-bags-women',
        'dior-women-shoes'            => 'dior-shoes-women',
        'dior-women-clothes'          => 'dior-clothes-women',
        'dior-men-shoes'              => 'dior-shoes-men',
        'dior-men-clothes'            => 'dior-clothes-men',
        
        // Hermès
        'hermes-women-bags'           => 'hermes-bags-women',
        'hermes-women-shoes'          => 'hermes-shoes-women',
        'hermes-women-clothes'        => 'hermes-clothes-women',
        'hermes-men-shoes'            => 'hermes-shoes-men',
        'hermes-men-clothes'          => 'hermes-clothes-men',
        'hermes-women-belts'          => 'hermes-belts-women',
        'hermes-women-glasses'        => 'hermes-glasses-women',
        'hermes-women-jewelry'        => 'hermes-jewelry-women',
        
        default => null,
    };

    $folder    = $val('folder');
    $imageName = $val('image');

    // 1) Prefer stored image_path
    $imagePath = $val('image_path');

    // 2) If empty, build from baseFolder + folder + image
    if (! $imagePath && $baseFolder && $folder) {
        $filename  = $imageName ?: '0000.jpg';
        $imagePath = "imports/{$baseFolder}/{$folder}/{$filename}";
    }

    // Use optimized thumbnail service for card images
    $thumbnailService = app(ThumbnailService::class);

    if ($imagePath) {
        $img = $thumbnailService->getUrl($imagePath, 'card') ?? Storage::url($imagePath);
        $originalImg = Storage::url($imagePath);
    } else {
        $img = asset('assets/placeholders/product-dark.png');
        $originalImg = $img;
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
            width="400"
            height="300"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
            decoding="async"
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
