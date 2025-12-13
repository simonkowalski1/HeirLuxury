@props(['items' => null])

@php
    // Fallback to automatic breadcrumbs if the controller didn't pass any
    $items = $items ?? \App\Support\Breadcrumbs::for(request()) ?? [];

    // Make sure it's a plain array
    $items = is_array($items) ? $items : iterator_to_array($items);

    $count = count($items);
    $lastIndex = $count - 1;

    $structured = [];
    foreach ($items as $i => $crumb) {
        $entry = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $crumb['label'] ?? '',
        ];
        if (!empty($crumb['href'])) {
            $entry['item'] = $crumb['href'];
        }
        $structured[] = $entry;
    }
@endphp

@if($count > 1)
<nav
    aria-label="Breadcrumb"
    class="border-y border-white/10 bg-black"
>
    <ol
        class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-2 flex items-center gap-2 overflow-x-auto whitespace-nowrap text-sm leading-none"
    >
        @foreach($items as $i => $item)
            @if($i === 0)
                {{-- HOME CRUMB --}}
                <li class="flex items-center">
                    <a
                        href="{{ $item['href'] ?? route('home') }}"
                        class="inline-flex items-center gap-2 text-white hocus:underline"
                    >
                        <span class="text-white text-xs/none font-medium">
                            {{ $item['label'] ?? 'Home' }}
                        </span>
                    </a>
                </li>
            @else
                {{-- SEPARATOR --}}
                <li class="text-white/50 select-none flex items-center">/</li>

                {{-- CRUMB --}}
                <li class="flex items-center">
                    @if(!empty($item['href']) && $i !== $lastIndex)
                        <a
                            href="{{ $item['href'] }}"
                            class="text-white/80 hocus:underline"
                        >
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="font-medium text-white">
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endif
        @endforeach
    </ol>

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $structured,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
</nav>
@endif
