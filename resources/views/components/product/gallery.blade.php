{{-- resources/views/components/product/gallery.blade.php --}}
@props([
    'images' => [],
])

@php
    // Normalize to a plain 0-based array
    $images = array_values($images ?? []);
@endphp

@if (count($images))
    <div
        {{ $attributes->merge([
            'class' => 'grid grid-cols-[96px_1fr] gap-3 items-start',
        ]) }}
        x-data="{
            idx: 0,
            images: @js($images),
            heroLoaded: false,
            lightbox: false,
            setIndex(i) { this.idx = i; this.heroLoaded = false; },
            prev() { this.idx = (this.idx - 1 + this.images.length) % this.images.length; this.heroLoaded = false; },
            next() { this.idx = (this.idx + 1) % this.images.length; this.heroLoaded = false; },
        }"
    >
        {{-- Thumbnails --}}
        <nav
            class="overflow-y-auto overflow-x-hidden gold-scroll pl-0 pr-0 h-[520px]"
            aria-label="Thumbnails"
        >
            <ul class="space-y-2">
                @foreach($images as $i => $img)
                    <li>
                        <button
                            type="button"
                            class="group block w-24 h-24 rounded-md overflow-hidden border bg-black/40 focus:outline-none transition
                                   border-white/10 hover:border-amber-400"
                            :class="{ 'ring-2 ring-amber-400' : idx === {{ $i }} }"
                            @click="setIndex({{ $i }})"
                            @mouseenter="setIndex({{ $i }})"
                            @focus="setIndex({{ $i }})"
                            aria-controls="hero-main"
                            :aria-current="idx === {{ $i }}"

                        >
                            <img
                                src="{{ $img['src'] }}"
                                alt="{{ $img['alt'] ?? 'Thumbnail '.($i + 1) }}"
                                class="w-full h-full object-cover transition duration-200 will-change-transform
                                       group-hover:opacity-80 group-hover:scale-105"
                                loading="lazy"
                            >
                        </button>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Hero + lightbox ... --}}
        {{-- (rest of your file exactly as you pasted) --}}
@else
    <div class="aspect-[4/5] rounded-2xl border border-white/10 flex items-center justify-center text-white/40">
        No images available.
    </div>
@endif
