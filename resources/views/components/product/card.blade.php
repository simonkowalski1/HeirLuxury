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
    // Dynamically resolve category_slug ({brand}-{gender}-{section}) to storage folder ({prefix}-{section}-{gender})
    $baseFolder = null;
    $brandPrefixes = [
        'louis-vuitton' => 'lv',
        'chanel'        => 'chanel',
        'dior'          => 'dior',
        'hermes'        => 'hermes',
        'celine'        => 'celine',
        'givenchy'      => 'givenchy',
        'mcqueen'       => 'mcqueen',
        'moncler'       => 'moncler',
        'nike'          => 'nike',
        'offwhite'      => 'offwhite',
        'versace'       => 'versace',
        'yeezy'         => 'yeezy',
    ];

    if ($categorySlug) {
        foreach ($brandPrefixes as $brand => $prefix) {
            if (str_starts_with($categorySlug, "{$brand}-")) {
                $rest = substr($categorySlug, strlen("{$brand}-"));
                if (preg_match('/^(women|men)-(.+)$/', $rest, $matches)) {
                    $gender = $matches[1];
                    $section = $matches[2];
                    $baseFolder = "{$prefix}-{$section}-{$gender}";
                    break;
                }
            }
        }
    }

    $folder    = $val('folder');
    $imageName = $val('image');

    // 1) Prefer cover.jpg (scraped thumbnail) if it exists
    $imagePath = null;
    if ($baseFolder && $folder) {
        $coverPath = "imports/{$baseFolder}/{$folder}/cover.jpg";
        if (Storage::disk('public')->exists($coverPath)) {
            $imagePath = $coverPath;
        }
    }

    // 2) Fall back to stored image_path
    if (! $imagePath) {
        $imagePath = $val('image_path');
    }

    // 3) If still empty, build from baseFolder + folder + image
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
    <div class="px-5 py-4">
        <p class="text-sm font-semibold text-white tracking-wide text-center">
            {{ $name }}
        </p>
    </div>
</a>
