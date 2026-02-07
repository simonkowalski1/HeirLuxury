{{-- resources/views/catalog/mega.blade.php --}}

@php
    // Build sections from config if they weren't passed in

    $womenSections = $womenSections
        ?? collect(config('categories.women') ?? []);

    $menSections = $menSections
        ?? collect(config('categories.men') ?? []);

    $locale = app()->getLocale();

    // Helper to translate section labels (bags, shoes, clothes) but not brand names
    $translateSection = function($label) use ($locale) {
        $key = 'messages.' . strtolower($label);
        $translated = __($key, [], $locale);
        return $translated !== $key ? $translated : $label;
    };

    // Helper to translate item names (e.g., "Louis Vuitton Bags" → "Louis Vuitton Torby")
    // Translates category words (Bags, Shoes, Clothing, etc.) and gender (Women, Men) but keeps brand names
    $translateItemName = function($name) use ($locale) {
        $translations = [
            'Bags' => __('messages.bags', [], $locale),
            'Shoes' => __('messages.shoes', [], $locale),
            'Clothing' => __('messages.clothing', [], $locale),
            'Belts' => __('messages.belts', [], $locale),
            'Glasses' => __('messages.glasses', [], $locale),
            'Jewelry' => __('messages.jewelry', [], $locale),
            'Watches' => __('messages.watches', [], $locale),
            'Women' => __('messages.women', [], $locale),
            'Men' => __('messages.men', [], $locale),
        ];

        $result = $name;
        foreach ($translations as $english => $translated) {
            // Only replace if translation exists (not returning the key)
            if ($translated !== 'messages.' . strtolower($english)) {
                $result = str_replace($english, $translated, $result);
            }
        }
        return $result;
    };
@endphp


<div x-data="{ gender: 'women' }" class="mega-shell">
    {{-- GENDER TOGGLE (centered) --}}
    <div class="mega-genders">
        <button
            type="button"
            @click="gender = 'women'"
            :class="gender === 'women' ? 'is-active' : ''"
        >
            {{ __('messages.women') }}
        </button>

        <button
            type="button"
            @click="gender = 'men'"
            :class="gender === 'men' ? 'is-active' : ''"
        >
            {{ __('messages.men') }}
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
                    {{ strtoupper($translateSection($sectionLabel)) }}
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

        // Add locale parameter
        $params['locale'] = $locale;

        $url = route($routeName, $params);
    } else {
        // Fallback: plain href or '#'
        $url = $item['href'] ?? '#';
    }
@endphp


                        <li>
                            <a href="{{ $url }}">
                                {{ $translateItemName($item['label'] ?? $item['name'] ?? 'Unnamed') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>

    {{-- MEN PANEL (centered - only 3 columns) --}}
    <div
        x-show="gender === 'men'"
        x-transition.opacity
        class="mega-grid justify-center"
        style="grid-template-columns: repeat(3, minmax(0, max-content));"
    >
        @foreach ($menSections as $sectionLabel => $items)
            <section class="mega-col">
                <h3 class="mega-col-title">
                    {{ strtoupper($translateSection($sectionLabel)) }}
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

        // Add locale parameter
        $params['locale'] = $locale;

        $url = route($routeName, $params);
    } else {
        // Fallback: plain href or '#'
        $url = $item['href'] ?? '#';
    }
@endphp


                        <li>
                            <a href="{{ $url }}">
                                {{ $translateItemName($item['label'] ?? $item['name'] ?? 'Unnamed') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>
</div>
