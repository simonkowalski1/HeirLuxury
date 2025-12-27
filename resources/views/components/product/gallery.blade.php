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
        {{-- Thumbnails - uses optimized thumb size --}}
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
                                src="{{ $img['thumb'] ?? $img['src'] }}"
                                alt="{{ $img['alt'] ?? 'Thumbnail '.($i + 1) }}"
                                width="96"
                                height="96"
                                class="w-full h-full object-cover transition duration-200 group-hover:opacity-80 group-hover:scale-105"
                                loading="lazy"
                                decoding="async"
                            >
                        </button>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Hero image - uses optimized gallery size --}}
        <div class="relative h-[520px]">
            <button
                type="button"
                @click="lightbox = true"
                class="block w-full h-full rounded-2xl overflow-hidden border border-white/10 bg-black/40 cursor-zoom-in"
            >
                <img
                    :src="images[idx]?.src"
                    :alt="images[idx]?.alt || 'Product image'"
                    width="800"
                    height="800"
                    class="w-full h-full object-cover transition-opacity duration-300"
                    :class="heroLoaded ? 'opacity-100' : 'opacity-0'"
                    @load="heroLoaded = true"
                    decoding="async"
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

        {{-- Lightbox - uses original full-size image --}}
        <template x-teleport="body">
            <div
                x-show="lightbox"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="lightbox = false"
                @keydown.escape.window="lightbox = false"
                @keydown.left.window="if(lightbox) prev()"
                @keydown.right.window="if(lightbox) next()"
                class="fixed inset-0 z-50 bg-black/95 flex items-center justify-center p-4 cursor-zoom-out"
                x-data="{ lightboxLoaded: false }"
                x-init="$watch('lightbox', value => { if(value) lightboxLoaded = false })"
            >
                {{-- Loading spinner --}}
                <div
                    x-show="!lightboxLoaded"
                    class="absolute inset-0 flex items-center justify-center"
                >
                    <svg class="animate-spin h-10 w-10 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                {{-- Lightbox image --}}
                <img
                    x-show="lightboxLoaded"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    :src="images[idx]?.original || images[idx]?.src"
                    :alt="images[idx]?.alt || 'Product image'"
                    class="max-w-full max-h-full object-contain"
                    @load="lightboxLoaded = true"
                    @click.stop
                >

                {{-- Close button --}}
                <button
                    type="button"
                    @click="lightbox = false"
                    class="absolute top-4 right-4 w-10 h-10 rounded-full bg-black/60 border border-white/20
                           text-white flex items-center justify-center hover:bg-amber-400 hover:text-black
                           hover:border-amber-400 transition"
                    aria-label="Close lightbox"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Navigation arrows in lightbox --}}
                <template x-if="images.length > 1">
                    <div>
                        <button
                            type="button"
                            @click.stop="prev(); lightboxLoaded = false"
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-black/60
                                   border border-white/20 text-white flex items-center justify-center
                                   hover:bg-amber-400 hover:text-black hover:border-amber-400 transition"
                            aria-label="Previous image"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            @click.stop="next(); lightboxLoaded = false"
                            class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-black/60
                                   border border-white/20 text-white flex items-center justify-center
                                   hover:bg-amber-400 hover:text-black hover:border-amber-400 transition"
                            aria-label="Next image"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </template>

                {{-- Image counter --}}
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/60 text-white text-sm px-4 py-2 rounded-full border border-white/10">
                    <span x-text="idx + 1"></span> / <span x-text="images.length"></span>
                </div>
            </div>
        </template>
    </div>
@else
    <div class="h-[520px] rounded-2xl border border-white/10 flex items-center justify-center text-white/40">
        No images available.
    </div>
@endif