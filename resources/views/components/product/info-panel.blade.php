@props(['product'])

<div class="mt-4 text-white/80">
  <p class="mb-2">Brand: {{ $product->brand ?? 'Heir Luxury' }}</p>

  @if(isset($product->price))
    <p class="mb-2">Price: ${{ number_format($product->price, 2) }}</p>
  @endif

  <p class="mt-4 text-sm text-white/60">
    {{ $product->description ?? 'Details coming soon.' }}
  </p>
</div>
