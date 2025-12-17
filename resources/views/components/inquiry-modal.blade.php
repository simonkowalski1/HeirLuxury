@props([
  'to' => '/inquiry',
  'product' => null,
])

<div
  x-data="{
    open: false,
    loading: false,
    sent: false,
    error: '',
    to: @js($to),
    product: @js($product ? [
      'name' => $product['name'] ?? ($product->name ?? null),
      'slug' => $product['slug'] ?? ($product->slug ?? null),
      'url'  => request()->fullUrl(),
    ] : null),
    form: {
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      message: ''
    },
    init() {
      window.addEventListener('open-inquiry-modal', (e) => {
        if (e.detail?.product) {
          this.product = e.detail.product;
        }
        this.open = true;
      });
    },
    close() {
      this.open = false;
      this.error = '';
      if (this.sent) {
        this.form = { first_name: '', last_name: '', email: '', phone: '', message: '' };
        this.sent = false;
      }
    },
    async submit() {
      this.error = '';
      this.loading = true;

      try {
        const payload = {
          ...this.form,
          product_name: this.product?.name || null,
          product_slug: this.product?.slug || null,
          product_url: this.product?.url || null,
        };

        const response = await fetch(this.to, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}'
          },
          body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (response.ok && data.success) {
          this.sent = true;
        } else {
          this.error = data.message || 'Something went wrong. Please try again.';
        }
      } catch (e) {
        this.error = 'Network error. Please check your connection and try again.';
      } finally {
        this.loading = false;
      }
    }
  }"
  x-cloak
  x-show="open"
  x-transition.opacity
  @keydown.escape.window.stop="close()"
  class="fixed inset-0 z-[100] flex"
  style="display:none;"
>
  {{-- Backdrop --}}
  <div class="absolute inset-0 bg-black/60"
       @click.stop.prevent="open=false"></div>

  {{-- Panel --}}
  <section class="relative ml-auto h-full w-full max-w-md bg-slate-900 border-l border-white/10 shadow-xl
                  flex flex-col"
           x-transition.duration.200ms
           @click.stop>
    <header class="px-5 h-14 flex items-center justify-between border-b border-white/10">
      <h3 class="text-sm font-semibold text-white" x-text="product?.name ? 'Product inquiry' : 'Contact us'"></h3>
      <button class="p-2 rounded hover:bg-white/5 text-zinc-400 hover:text-white"
              type="button"
              @click.stop.prevent="close()"
              aria-label="Close">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </header>

    {{-- Context --}}
    <template x-if="product?.name">
      <div class="px-5 pt-3 text-xs text-zinc-400 space-y-1">
        <div class="font-medium text-white" x-text="product.name"></div>
        <div class="truncate text-zinc-500" x-text="product.url"></div>
      </div>
    </template>

    {{-- Form --}}
    <form class="flex-1 overflow-auto px-5 py-4 space-y-4" @submit.prevent="submit">
      <div class="grid grid-cols-2 gap-3">
        <input type="text"
               class="w-full rounded-lg bg-zinc-800 border border-white/10 px-3 py-2 text-sm text-white placeholder-zinc-500 outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/50"
               placeholder="First name"
               x-model="form.first_name"
               required>
        <input type="text"
               class="w-full rounded-lg bg-zinc-800 border border-white/10 px-3 py-2 text-sm text-white placeholder-zinc-500 outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/50"
               placeholder="Last name"
               x-model="form.last_name"
               required>
      </div>
      <input type="email"
             class="w-full rounded-lg bg-zinc-800 border border-white/10 px-3 py-2 text-sm text-white placeholder-zinc-500 outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/50"
             placeholder="Email"
             x-model="form.email"
             required>
      <input type="tel"
             class="w-full rounded-lg bg-zinc-800 border border-white/10 px-3 py-2 text-sm text-white placeholder-zinc-500 outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/50"
             placeholder="Phone (optional)"
             x-model="form.phone">
      <textarea rows="4"
                class="w-full rounded-lg bg-zinc-800 border border-white/10 px-3 py-2 text-sm text-white placeholder-zinc-500 outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/50 resize-none"
                placeholder="Message"
                x-model="form.message"
                required></textarea>

      <p class="text-xs text-red-400" x-show="error" x-text="error"></p>
      <p class="text-xs text-green-400" x-show="sent">Thanks! We'll get back to you shortly.</p>

      <div class="flex justify-end gap-2 pt-2">
        <button type="button"
                class="rounded-lg bg-zinc-700 text-white px-4 py-2 text-sm font-medium hover:bg-zinc-600 transition-colors"
                @click.stop.prevent="close()">Cancel</button>
        <button type="submit"
                class="rounded-lg bg-amber-400 text-black px-4 py-2 text-sm font-medium hover:bg-amber-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="loading"
                x-text="loading ? 'Sending...' : 'Send'"></button>
      </div>
    </form>
  </section>
</div>
