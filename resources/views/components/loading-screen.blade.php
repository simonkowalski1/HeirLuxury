{{-- resources/views/components/loading-screen.blade.php --}}
{{-- Loading animation that transitions logo from center to hero section --}}
{{-- Only shows on first visit (uses sessionStorage to track) --}}
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
        src="{{ asset('img/hl-logo-loading.png') }}"
        alt="Heir Luxury"
        :class="animating ? 'logo-animate' : ''"
        class="w-32 h-32 object-contain"
    >
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('loadingScreen', () => ({
        visible: false,
        animating: false,

        init() {
            // Only show loading animation on first visit (per session)
            const hasVisited = sessionStorage.getItem('hl_visited');

            if (hasVisited) {
                // Already visited - skip animation, show content immediately
                this.visible = false;
                window.dispatchEvent(new CustomEvent('loading-complete'));
                return;
            }

            // First visit - show animation
            sessionStorage.setItem('hl_visited', 'true');
            this.visible = true;

            // Start animation after a brief delay
            setTimeout(() => {
                this.startAnimation();
            }, 500);
        },

        startAnimation() {
            this.animating = true;
        },

        onAnimationEnd() {
            // Dispatch event to show the logo in the hero
            window.dispatchEvent(new CustomEvent('loading-complete'));
            this.visible = false;
        }
    }));
});
</script>
