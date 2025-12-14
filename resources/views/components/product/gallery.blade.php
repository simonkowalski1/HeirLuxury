{{-- resources/views/components/product/gallery.blade.php --}}
@props([
    'images' => [],
])

@php
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
        <nav class="overflow-y-auto overflow-x-hidden gold-scroll h-[520px]" aria-label="Thumbnails">
            <ul class="space-y-2">
                @foreach($images as $i => $img)
                    <li>
                        <button
                            type="button"
                            class="group block w-24 h-24 rounded-md overflow-hidden border bg-black/40 focus:outline-none transition border-white/10 hover:border-amber-400"
                            :class="{ 'ring-2 ring-amber-400' : idx === {{ $i }} }"
                            @click="setIndex({{ $i }})"
                            @mouseenter="setIndex({{ $i }})"
                            @focus="setIndex({{ $i }})"
                        >
                            <img
                                src="{{ $img['src'] }}"
                                alt="{{ $img['alt'] ?? 'Thumbnail '.($i + 1) }}"
                                class="w-full h-full object-cover transition duration-200 group-hover:opacity-80 group-hover:scale-105"
                                loading="lazy"
                            >
                        </button>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Hero image --}}
        <div class="relative h-[520px]">
            <button
                type="button"
                @click="lightbox = true"
                class="block w-full h-full rounded-2xl overflow-hidden border border-white/10 bg-black/40 cursor-zoom-in"
            >
                <img
                    :src="images[idx]?.src"
                    :alt="images[idx]?.alt || 'Product image'"
                    class="w-full h-full object-cover transition-opacity duration-300"
                    :class="heroLoaded ? 'opacity-100' : 'opacity-0'"
                    @load="heroLoaded = true"
                >
            </button>

            {{-- Bottom bar: counter + arrows --}}
            <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between">
                {{-- Photo counter --}}
                <span class="bg-black/60 text-white text-sm px-3 py-1.5 rounded-full border border-white/10">
                    <span x-text="idx + 1"></span> / <span x-text="images.length"></span>
                </span>

                {{-- Navigation arrows --}}
                <template x-if="images.length > 1">
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click.stop="prev()"
                            class="w-10 h-10 rounded-full bg-black/60 border border-white/10 text-white flex items-center justify-center hover:bg-amber-400 hover:text-black hover:border-amber-400 transition"
                            aria-label="Previous image"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            @click.stop="next()"
                            class="w-10 h-10 rounded-full bg-black/60 border border-white/10 text-white flex items-center justify-center hover:bg-amber-400 hover:text-black hover:border-amber-400 transition"
                            aria-label="Next image"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Lightbox --}}
        <template x-teleport="body">
            <div
                x-show="lightbox"
                x-transition.opacity
                @click="lightbox = false"
                @keydown.escape.window="lightbox = false"
                class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4 cursor-zoom-out"
            >
                <img :src="images[idx]?.src" :alt="images[idx]?.alt || 'Product image'" class="max-w-full max-h-full object-contain" @click.stop>
            </div>
        </template>
    </div>
@else
    <div class="h-[520px] rounded-2xl border border-white/10 flex items-center justify-center text-white/40">
        No images available.
    </div>
@endif