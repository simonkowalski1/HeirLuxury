{{-- resources/views/components/loading-screen.blade.php --}}
{{-- Loading animation that transitions logo from center to hero section --}}
<div
    x-data="loadingScreen()"
    x-show="visible"
    x-transition:leave="transition-opacity ease-out duration-500"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @animationend="if ($event.target.classList.contains('logo-animate')) onAnimationEnd()"
    class="fixed inset-0 z-[100] bg-slate-950 flex items-center justify-center"
    x-cloak
>
    {{-- HL Logo Image --}}
    <img
        src="{{ asset('images/hl-logo-white.png') }}"
        alt="Heir Luxury"
        :class="animating ? 'logo-animate' : ''"
        class="w-32 h-32 object-contain transition-all"
        :style="animating ? getAnimationStyle() : ''"
    >
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('loadingScreen', () => ({
        visible: true,
        animating: false,
        targetRect: null,

        init() {
            // Start animation after a brief delay
            setTimeout(() => {
                this.startAnimation();
            }, 500);
        },

        startAnimation() {
            this.animating = true;
        },

        getAnimationStyle() {
            return `
                animation: logoToHero 2.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            `;
        },

        onAnimationEnd() {
            // Dispatch event to show the logo in the hero
            window.dispatchEvent(new CustomEvent('loading-complete'));
            this.visible = false;
        }
    }));
});
</script>

<style>
@keyframes logoToHero {
    0% {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
    50% {
        transform: scale(1.1) translateY(0);
        opacity: 1;
    }
    100% {
        transform: scale(0.5) translateY(-30vh);
        opacity: 0;
    }
}

.logo-animate {
    animation: logoToHero 2.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}
</style>
