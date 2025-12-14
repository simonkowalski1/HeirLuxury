{{-- resources/views/catalog/mega.blade.php --}}

@php
    // Build sections from config if they weren't passed in

    $womenSections = $womenSections
        ?? collect(config('categories.women') ?? []);

    $menSections = $menSections
        ?? collect(config('categories.men') ?? []);
@endphp


<div x-data="{ gender: 'women' }" class="mega-shell">
    {{-- GENDER TOGGLE (centered) --}}
    <div class="mega-genders">
        <button
            type="button"
            @click="gender = 'women'"
            :class="gender === 'women' ? 'is-active' : ''"
        >
            Women
        </button>

        <button
            type="button"
            @click="gender = 'men'"
            :class="gender === 'men' ? 'is-active' : ''"
        >
            Men
        </button>
    </div>

    {{-- WOMEN PANEL --}}
    <div
        x-show="gender === 'women'"
        x-transition.opacity
        class="mega-grid"
    >
        @foreach ($womenSections as $sectionLabel => $items)
            <section class="mega-col">
                <h3 class="mega-col-title">
                    {{ strtoupper($sectionLabel) }}
                </h3>

                <ul class="mega-list">
                    @foreach ($items as $item)
                        @php
    // Normalise legacy config so everything goes through `catalog.category`
    if (!empty($item['route'])) {
        $routeName = $item['route'];
        $params    = $item['params'] ?? [];

        // If config still says `categories.show`, convert it to the new canonical route
        // and map its old `slug` param to the new `{category:slug}` binding.
        if ($routeName === 'categories.show') {
            $routeName = 'catalog.category';

            // Legacy config: ['slug' => 'louis-vuitton-women-bags']
            if (isset($params['slug'])) {
                $params = ['category' => $params['slug']];
            }
        }

        $url = route($routeName, $params);
    } else {
        // Fallback: plain href or '#'
        $url = $item['href'] ?? '#';
    }
@endphp


                        <li>
                            <a href="{{ $url }}">
                                {{ $item['label'] ?? $item['name'] ?? 'Unnamed' }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>

    {{-- MEN PANEL --}}
    <div
        x-show="gender === 'men'"
        x-transition.opacity
        class="mega-grid"
    >
        @foreach ($menSections as $sectionLabel => $items)
            <section class="mega-col">
                <h3 class="mega-col-title">
                    {{ strtoupper($sectionLabel) }}
                </h3>

                <ul class="mega-list">
                    @foreach ($items as $item)
                        @php
    // Normalise legacy config so everything goes through `catalog.category`
    if (!empty($item['route'])) {
        $routeName = $item['route'];
        $params    = $item['params'] ?? [];

        // If config still says `categories.show`, convert it to the new canonical route
        // and map its old `slug` param to the new `{category:slug}` binding.
        if ($routeName === 'categories.show') {
            $routeName = 'catalog.category';

            // Legacy config: ['slug' => 'louis-vuitton-women-bags']
            if (isset($params['slug'])) {
                $params = ['category' => $params['slug']];
            }
        }

        $url = route($routeName, $params);
    } else {
        // Fallback: plain href or '#'
        $url = $item['href'] ?? '#';
    }
@endphp


                        <li>
                            <a href="{{ $url }}">
                                {{ $item['label'] ?? $item['name'] ?? 'Unnamed' }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>
</div>
