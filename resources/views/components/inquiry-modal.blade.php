@props([
  // where to send the form (if you use it)
  'to' => '/inquiry',
  // optional product context (can be null)
  'product' => null,
])

<div
  x-data="inquiryModal({
    to: @js($to),
    product: @js($product ? [
      'name' => $product['name'] ?? ($product->name ?? null),
      'slug' => $product['slug'] ?? ($product->slug ?? null),
      'url'  => request()->fullUrl(),
    ] : null)
  })"
  x-init="init()" 
  x-cloak
  x-show="open"
  x-transition.opacity
  @keydown.escape.window.stop="close()"
  class="fixed inset-0 z-[100] flex"
  style="display:none;"
>
  {{-- Backdrop (INSIDE the component scope) --}}
  <div class="absolute inset-0 bg-black/60"
       @click.stop.prevent="open=false"></div>

  {{-- Panel --}}
  <section class="relative ml-auto h-full w-full max-w-md bg-surface border-l border-white/10 shadow-xl
                  flex flex-col"
           x-transition.duration.200ms
           @click.stop>
    <header class="px-5 h-14 flex items-center justify-between border-b border-white/10">
      <h3 class="text-sm font-semibold" x-text="product?.name ? 'Product inquiry' : 'Contact us'"></h3>
      <button class="p-2 rounded hover:bg-white/5"
              type="button"
              @click.stop.prevent="close()"
              aria-label="Close">✕</button>
    </header>

    {{-- Context --}}
    <div class="px-5 pt-3 text-xs text-muted space-y-1">
      <div class="font-medium text-ink" x-text="product?.name || ''"></div>
      <div class="truncate" x-text="product?.url || ''"></div>
    </div>

    {{-- Form (optional) --}}
    <form class="flex-1 overflow-auto px-5 py-4 space-y-4" @submit.prevent="submit">
      <div class="grid grid-cols-2 gap-3">
        <input type="text" class="input" placeholder="First name" x-model="form.first_name">
        <input type="text" class="input" placeholder="Last name"  x-model="form.last_name">
      </div>
      <input type="email" class="input" placeholder="Email" x-model="form.email">
      <input type="tel"   class="input" placeholder="Phone (optional)" x-model="form.phone">
      <textarea rows="4" class="input" placeholder="Message" x-model="form.message"></textarea>

      <p class="text-xs text-red-400" x-show="error" x-text="error"></p>
      <p class="text-xs text-green-400" x-show="sent">Thanks! We’ll get back to you shortly.</p>

      <div class="flex justify-end gap-2">
        <button type="button" class="btn" @click.stop.prevent="close()">Cancel</button>
        <button type="submit" class="btn-primary" :disabled="loading"
                x-text="loading ? 'Sending…' : 'Send'"></button>
      </div>
    </form>
  </section>
</div>
