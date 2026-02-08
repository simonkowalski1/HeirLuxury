{{-- resources/views/wishlist/dropdown.blade.php --}}
{{-- Wishlist dropdown panel — lives inside navbar's Alpine scope --}}

{{-- Wishlist backdrop --}}
<div
    x-show="$store.wishlist.open"
    x-transition.opacity
    x-cloak
    @click="$store.wishlist.open = false"
    style="z-index: 9998;"
    class="fixed inset-0 bg-black/60"
></div>

{{-- Wishlist panel --}}
<section
    x-show="$store.wishlist.open"
    x-cloak
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-1"
    @keydown.escape.window="$store.wishlist.open = false"
    style="z-index: 9999;"
    class="fixed right-4 top-14 w-80 sm:w-96 max-h-[70vh] rounded-2xl
           bg-slate-950/95 border border-yellow-500/30 shadow-2xl shadow-black/60
           backdrop-blur-xl overflow-hidden flex flex-col"
>
    {{-- Header --}}
    <header class="flex items-center justify-between px-5 py-3 border-b border-white/10">
        <h3 class="text-sm font-semibold tracking-wider uppercase text-slate-200">
            {{ __('messages.wishlist') }}
            <span
                x-show="$store.wishlist.count > 0"
                x-text="'(' + $store.wishlist.count + ')'"
                class="text-amber-400"
            ></span>
        </h3>
        <button
            type="button"
            @click="$store.wishlist.open = false"
            class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-white/10 transition"
            aria-label="Close wishlist"
        >
            <svg class="h-4 w-4 text-white/70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M6 18L18 6" />
            </svg>
        </button>
    </header>

    {{-- Items list --}}
    <div class="overflow-y-auto gold-scroll flex-1">
        {{-- Loading state --}}
        <div x-show="$store.wishlist.loading" class="flex items-center justify-center py-8">
            <svg class="animate-spin h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        {{-- Empty state --}}
        <div
            x-show="!$store.wishlist.loading && $store.wishlist.items.length === 0"
            class="flex flex-col items-center justify-center py-10 px-6 text-center"
        >
            <svg class="w-10 h-10 text-white/20 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
            </svg>
            <p class="text-sm text-white/50">{{ __('messages.wishlist_empty') }}</p>
            <p class="text-xs text-white/30 mt-1">{{ __('messages.wishlist_empty_description') }}</p>
        </div>

        {{-- Product items --}}
        <template x-for="item in $store.wishlist.items" :key="item.id">
            <div class="flex items-center gap-3 px-4 py-3 border-b border-white/5 hover:bg-white/5 transition">
                {{-- Product name + brand (clickable link to product page) --}}
                <a :href="item.url" class="flex-1 min-w-0 group">
                    <p class="text-sm text-white truncate group-hover:text-amber-400 transition" x-text="item.name"></p>
                    <p class="text-xs text-white/40" x-text="item.brand"></p>
                </a>

                {{-- Remove button --}}
                <button
                    type="button"
                    @click="$store.wishlist.toggle(item.id)"
                    class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full
                           hover:bg-red-500/20 text-white/40 hover:text-red-400 transition"
                    aria-label="{{ __('messages.remove_from_wishlist') }}"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M6 18L18 6" />
                    </svg>
                </button>
            </div>
        </template>
    </div>
</section>
