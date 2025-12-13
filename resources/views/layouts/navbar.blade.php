{{-- resources/views/layouts/navbar.blade.php --}}

<nav
    x-data="{ open:false, mobile:false, gender:'women' }"
    x-init="
        $watch('open',   value => document.body.classList.toggle('is-menu-open', value || mobile));
        $watch('mobile', value => document.body.classList.toggle('is-menu-open', value || open));
    "
    @keydown.escape.window="open = false; mobile = false"
    class="lux-nav"
>
    <div class="lux-nav-inner">
        {{-- LEFT: LOGO --}}
        <a href="{{ route('home') }}" class="lux-logo">
            <span class="lux-logo-heir">HEIR</span>
            <span class="lux-logo-luxury">LUXURY</span>
        </a>

        {{-- CENTER: CATALOG LABEL (DESKTOP) --}}
        <button
    type="button"
    @click="open = !open"
    :aria-expanded="open"
    class="catalog-toggle"
>
    <span class="catalog-line"></span>
    <span class="catalog-label">Catalog</span>
    <span class="catalog-line"></span>
</button>


        {{-- RIGHT: SEARCH + CONTACT (DESKTOP) --}}
        <div class="hidden lg:flex items-center gap-4">
            

            <button
                type="button"
                onclick="window.dispatchEvent(new Event('open-contact-modal'))"
                class="btn-gold px-4 py-1.5 text-xs"
            >
                Contact
            </button>
        </div>

        {{-- MOBILE: CONTACT + HAMBURGER --}}
        <div class="flex items-center gap-2 lg:hidden">
            <button
                type="button"
                onclick="window.dispatchEvent(new Event('open-contact-modal'))"
                class="btn-gold text-[11px] px-3 py-1.5"
            >
                Contact
            </button>

            <button
                type="button"
                @click="mobile = true"
                class="inline-flex h-10 w-10 items-center justify-center rounded-md hover:bg-white/10"
                aria-label="Open menu"
            >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    {{-- ========== DARK BACKDROP (closes both mega + mobile) ========== --}}
    <div
        x-show="open || mobile"
        x-transition.opacity
        x-cloak
        @click="open = false; mobile = false"
        class="fixed inset-0 bg-black/60 z-40"
    ></div>

    {{-- ========== DESKTOP MEGA PANEL ========== --}}
    <section
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="mega-panel hidden lg:block"
    >
        <div class="mega-scroll gold-scroll">
            @include('catalog.mega')
        </div>

        {{-- Gold line --}}
        <div class="mega-bottom-border"></div>
    </section>

    {{-- ========== MOBILE SLIDE-UP SHEET ========== --}}
    <section
        x-show="mobile"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="mobile-sheet lg:hidden"
        role="dialog"
        aria-label="Catalog navigation"
    >
        <header class="flex h-14 items-center justify-between border-b border-white/10 px-4">
            <span class="text-xs font-semibold tracking-[0.32em] uppercase text-slate-200">Catalog</span>

            <button
                type="button"
                @click="mobile = false"
                class="inline-flex h-10 w-10 items-center justify-center rounded-md hover:bg-white/10"
                aria-label="Close menu"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M6 18L18 6" />
                </svg>
            </button>
        </header>

        <div class="mobile-scroll gold-scroll p-6 space-y-6">
            <div class="mega-tabs">
                <button
                    type="button"
                    @click="gender = 'women'"
                    :class="['mega-gender', gender === 'women' ? 'active' : '']"
                >
                    Women
                </button>

                <button
                    type="button"
                    @click="gender = 'men'"
                    :class="['mega-gender', gender === 'men' ? 'active' : '']"
                >
                    Men
                </button>
            </div>

            <div x-show="gender === 'women'" x-transition.opacity class="space-y-6">
                @include('catalog.mega', ['gender' => 'women'])
            </div>

            <div x-show="gender === 'men'" x-transition.opacity class="space-y-6">
                @include('catalog.mega', ['gender' => 'men'])
            </div>
        </div>
    </section>
</nav>
