{{-- resources/views/catalog/_sidenav.blade.php --}}

@php
    use Illuminate\Support\Str;

    // Catalog data (same structure used by the mega menu)
    $catalog = $catalog ?? config('categories');

    // Try to figure out the current category slug from:
    //  - route model binding: {category:slug}
    //  - or second URL segment: /catalog/{slug}
    $routeCategory = request()->route('category');
    $activeSlug    = $activeSlug
        ?? ($routeCategory?->slug ?? request()->segment(2));

    /*
     |--------------------------------------------------------------
     | Determine which gender should be open by default
     |--------------------------------------------------------------
     | 1) Prefer an exact match in the catalog tree
     | 2) Fall back to a slug-based guess using -women- / -men-
     */

    $currentGender = 'women'; // sensible default

    if ($activeSlug) {
        $detected = null;

        // 1) Try to find the slug inside the catalog structure
        foreach (['women', 'men'] as $genderKey) {
            $sections = $catalog[$genderKey] ?? [];

            foreach ($sections as $sectionLabel => $items) {
                foreach ($items as $item) {
                    $params = $item['params'] ?? [];
                    $slug   = $params['category'] ?? $params['slug'] ?? null;

                    if ($slug === $activeSlug) {
                        $detected = $genderKey;
                        break 3; // break all three loops
                    }
                }
            }
        }

        if ($detected) {
            $currentGender = $detected;
        } else {
            // 2) Fallback: infer from slug text
            if (
                Str::contains($activeSlug, '-women-') ||
                Str::endsWith($activeSlug, '-women')
            ) {
                $currentGender = 'women';
            } elseif (
                Str::contains($activeSlug, '-men-') ||
                Str::endsWith($activeSlug, '-men')
            ) {
                $currentGender = 'men';
            }
        }
    }
@endphp

<aside class="catalog-sidenav">
    <div x-data="{ openGender: '{{ $currentGender }}' }" class="catalog-sidenav-inner gold-scroll">
        @foreach (['women' => 'Women', 'men' => 'Men'] as $genderKey => $genderLabel)
            @php
                $sections = $catalog[$genderKey] ?? [];
            @endphp

            <section class="side-nav-group">
                {{-- Gender toggle row --}}
                <button
                    type="button"
                    @click="openGender = (openGender === '{{ $genderKey }}' ? null : '{{ $genderKey }}')"
                    class="side-nav-toggle"
                >
                    <span>{{ $genderLabel }}</span>

                    <svg
                        class="h-3 w-3 transition-transform duration-150"
                        :class="openGender === '{{ $genderKey }}' ? 'rotate-180' : ''"
                        viewBox="0 0 20 20"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <path d="M5 7l5 5 5-5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                {{-- Sections + links --}}
                <div
                    x-show="openGender === '{{ $genderKey }}'"
                    x-transition.opacity
                    class="side-nav-sections"
                >
                    @foreach ($sections as $sectionLabel => $items)
                        <div class="side-nav-section">
                            <p class="side-nav-section-title">
                                {{ strtoupper($sectionLabel) }}
                            </p>

                            <ul class="side-nav-list">
                                @foreach ($items as $item)
                                    @php
                                        // Normalise URL building so we stay in sync with the mega menu
                                        $routeName = $item['route'] ?? null;
                                        $params    = $item['params'] ?? [];
                                        $href      = $item['href'] ?? '#';
                                        $slug      = $params['category'] ?? $params['slug'] ?? null;

                                        if ($routeName) {
                                            if ($routeName === 'categories.show') {
                                                // Legacy config: ['slug' => 'louis-vuitton-women-bags']
                                                if (isset($params['slug'])) {
                                                    $slug = $params['slug'];
                                                    $href = route('catalog.category', [
                                                        'category' => $slug,
                                                    ]);
                                                }
                                            } else {
                                                $href = route($routeName, $params);
                                            }
                                        }

                                        $isActive = $slug && $slug === $activeSlug;
                                    @endphp

                                    <li>
                                        <a
                                            href="{{ $href }}"
                                            class="side-nav-link {{ $isActive ? 'side-nav-link--active' : '' }}"
                                        >
                                            {{ $item['label'] ?? $item['name'] ?? 'Unnamed' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</aside>
