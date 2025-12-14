{{-- resources/views/catalog/_sidenav.blade.php --}}
@php
    use Illuminate\Support\Str;

    // Catalog data
    $catalog = $catalog ?? (array) config('categories', []);

    // Active slug: prefer injected value, else route param, else URL segment
    $activeSlug = $activeSlug
        ?? ((string) request()->route('category') ?: request()->segment(2));

    // Default open gender
    $currentGender = 'women';

    if ($activeSlug) {
        // Deterministic for group slugs
        if (Str::startsWith($activeSlug, 'men-') || $activeSlug === 'men') {
            $currentGender = 'men';
        } elseif (Str::startsWith($activeSlug, 'women-') || $activeSlug === 'women') {
            $currentGender = 'women';
        } else {
            // Detect by scanning config items
            foreach (['women', 'men'] as $genderKey) {
                $sections = $catalog[$genderKey] ?? [];
                foreach ($sections as $sectionLabel => $items) {
                    foreach ($items as $item) {
                        $params = $item['params'] ?? [];
                        $slug = $params['category'] ?? ($params['slug'] ?? null);
                        if ($slug === $activeSlug) {
                            $currentGender = $genderKey;
                            break 3;
                        }
                    }
                }
            }
        }
    }
@endphp

<aside class="catalog-sidenav">
    <div x-data="{ openGender: '{{ $currentGender }}' }" class="catalog-sidenav-inner gold-scroll">
        @foreach (['women' => 'Women', 'men' => 'Men'] as $genderKey => $genderLabel)
            @php $sections = $catalog[$genderKey] ?? []; @endphp

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
                                        $name   = $item['label'] ?? $item['name'] ?? 'Unnamed';
                                        $params = $item['params'] ?? [];
                                        $href   = $item['href'] ?? '#';

                                        // Normalize legacy param key
if (isset($params['slug']) && !isset($params['category'])) {
    $params['category'] = $params['slug'];
    unset($params['slug']);
}

                                        // Canonical route for category links
                                        if (!empty($item['route'])) {
                                            $routeName = $item['route'] === 'categories.show'
                                                ? 'catalog.category'
                                                : $item['route'];

                                            try {
                                                $href = route($routeName, $params);
                                            } catch (\Throwable $e) {
                                                // keep fallback $href
                                            }
                                        }

                                        $slug = $params['category'] ?? null;
                                        $isActive = $slug && $slug === $activeSlug;
                                    @endphp

                                    <li>
                                        <a
                                            href="{{ $href }}"
                                            class="side-nav-link {{ $isActive ? 'side-nav-link--active' : '' }}"
                                        >
                                            {{ $name }}
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
