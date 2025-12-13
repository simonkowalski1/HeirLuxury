{{-- resources/views/contact/modal.blade.php --}}

<div
    x-data="{ open: false }"
    x-on:open-contact-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/70"
        @click="open = false"
    ></div>

    {{-- Dialog --}}
    <div class="relative z-10 w-full max-w-5xl mx-4">
        <div class="rounded-3xl border border-yellow-500/30 bg-slate-950/95 shadow-2xl">
            {{-- Close button --}}
            <button
                type="button"
                @click="open = false"
                class="absolute right-5 top-5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-black/40 text-slate-300 hover:bg-black/70 hover:text-white transition"
                aria-label="Close contact form"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M6 18L18 6" />
                </svg>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 p-8 md:p-10">
                {{-- ================= LEFT: MESSAGE FORM ================= --}}
                <div class="flex flex-col gap-6">
                    <header class="space-y-2">
                        <h2 class="text-2xl font-semibold text-white">
                            Get in Touch
                        </h2>
                        <p class="text-sm text-slate-300 max-w-md">
                            We’d love to hear from you. Please include your
                            <span class="font-semibold text-yellow-300">name</span>
                            and
                            <span class="font-semibold text-yellow-300">number</span>.
                        </p>
                    </header>

                    <form method="POST" action="#" class="flex flex-col flex-1 gap-4">
                        @csrf {{-- keep / adjust when you wire up the backend --}}

                        <div class="flex-1 flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-100">
                                Message
                            </label>

                            <textarea
                                name="message"
                                class="flex-1 w-full resize-none rounded-xl border border-yellow-400/70 bg-black/40 px-4 py-3 text-sm text-slate-100 placeholder-slate-500 outline-none focus:border-yellow-300 focus:ring-1 focus:ring-yellow-300 transition"
                                rows="6"
                                placeholder="Your message..."
                            ></textarea>
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-full bg-yellow-400 px-6 py-2 text-sm font-semibold text-black shadow-md hover:bg-yellow-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-yellow-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950 transition"
                            >
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ================= RIGHT: CUSTOMER SERVICE ================= --}}
                <div class="flex flex-col border-t md:border-t-0 md:border-l border-white/10 pt-6 md:pt-0 md:pl-8">
                    {{-- Top block: heading + blurb --}}
                    <div class="space-y-3">
                        <h3 class="text-sm font-semibold tracking-[0.25em] text-yellow-300 uppercase">
                            Customer Service
                        </h3>

                        <p class="text-sm text-slate-300 leading-relaxed">
                            For all questions concerning catalog inquiries, please
                            reach out to:
                        </p>
                    </div>

                    {{-- Bottom block: aligned with bottom of message box --}}
<div class="mt-auto space-y-3 pt-6 text-sm text-slate-200">
    <p>
        <span class="font-semibold text-slate-100">Phone:</span>
        <span class="ml-2 text-slate-300">1-(862)-440-7843</span>
    </p>

    <p>
        <span class="font-semibold text-slate-100">Email:</span>
        <span class="ml-2 text-slate-300">
            Lhboss06@gmail.com
            
        </span>
    </p>

    {{-- Telegram on a single row --}}
    <div class="flex items-center gap-3">
        <span class="font-semibold text-slate-100 whitespace-nowrap">
            Telegram:
        </span>

        <a
            href="https://t.me/HEIRLUXURYCLOTHING"
            target="_blank"
            class="inline-flex items-center gap-2 rounded-full bg-black/40 px-4 py-1.5 text-sm text-slate-100 
                   hover:bg-black/60 transition w-max whitespace-nowrap"
        >
            <img
                src="{{ asset('img/telegram.png') }}"
                alt="Telegram"
                class="h-4 w-4 shrink-0"
            />

            <span class="font-medium tracking-wide whitespace-nowrap">
                @HEIRLUXURYCLOTHING
            </span>
        </a>
    </div>
</div>
