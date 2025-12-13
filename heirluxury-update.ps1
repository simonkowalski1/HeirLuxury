# heirluxury-update.ps1
# Run from your Laravel project root:
#   pwsh ./heirluxury-update.ps1
# or:
#   powershell -ExecutionPolicy Bypass -File .\heirluxury-update.ps1

$ErrorActionPreference = "Stop"

function Write-File($Path, $Content) {
  $dir = Split-Path -Parent $Path
  if ($dir -and !(Test-Path $dir)) {
    New-Item -ItemType Directory -Path $dir | Out-Null
  }
  $Content | Out-File -FilePath $Path -Encoding UTF8 -Force
  Write-Host "Wrote: $Path"
}


# --- A) Product page -------------------------------------------------------
$productsShow = @'
@extends('layouts.app')

@php
  $gallery = collect($product->images ?? [])
    ->map(fn($img) => is_array($img)
      ? ['src' => $img['src'] ?? $img['url'] ?? '', 'alt' => $img['alt'] ?? '']
      : (is_object($img) ? ['src' => $img->url, 'alt' => $img->alt ?? ''] : ['src' => (string)$img, 'alt' => '']))
    ->values()->all();

  $categoryName = $product->category->name ?? 'Catalog';
  $categorySlug = $product->category->slug ?? 'all';

  $comingSoon  = ($product->status ?? null) === 'coming_soon';
  $availableAt = $product->available_at ?? null;
  $availableTz = $availableAt ? $availableAt->timezone(config('app.timezone','America/New_York')) : null;
  $availableTxt = $availableTz ? ($availableTz->format('n/j') . ' at ' . $availableTz->format('g:i A T')) : null;

  $bullets = is_iterable($product->bullets ?? null) ? $product->bullets : [];
@endphp

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8 p-6">
  <div>
    <x-product-gallery :images="$gallery" class="gold-scroll" />
  </div>

  <x-product.info-panel
    :product="$product"
    :categoryName="$categoryName"
    :categorySlug="$categorySlug"
    :comingSoon="$comingSoon"
    :availableTxt="$availableTxt"
    :bullets="$bullets"
    :showMeta="true"
  />
</div>
@endsection
'@

Write-File "resources/views/products/show.blade.php" $productsShow

# --- B) Components: info-panel, coming-soon, meta, card -------------------
$infoPanel = @'
@props([
  'product',
  'categoryName' => 'Catalog',
  'categorySlug' => 'all',
  'comingSoon' => false,
  'availableTxt' => null,
  'bullets' => [],
  'showMeta' => true,
])

<aside class="space-y-4">
  <h1 class="text-2xl font-semibold text-ink">{{ $product->name }}</h1>
  <a href="{{ route('catalog', $categorySlug) }}" class="text-muted hover:text-ink">
    {{ $categoryName }}
  </a>

  @if($comingSoon)
    <x-product.coming-soon :availableTxt="$availableTxt" />
  @endif

  <hr class="border-t border-thin my-4">

  @if($product->summary)
    <p class="text-ink/90">{{ $product->summary }}</p>
  @endif

  @if(!empty($product->description))
    <div class="prose prose-invert max-w-none">{!! $product->description !!}</div>
  @endif

  @if(!empty($bullets))
    <ul class="list-disc pl-5 space-y-1 text-ink">
      @foreach($bullets as $b) <li>{{ $b }}</li> @endforeach
    </ul>
  @endif

  @if($showMeta)
    <x-product.meta :product="$product" />
  @endif
</aside>
'@
Write-File "resources/views/components/product/info-panel.blade.php" $infoPanel

$comingSoon = @'
@props(['availableTxt' => null])

<div class="bg-surface border border-thin rounded-lg p-4 space-y-2">
  <div class="inline-block bg-brand text-black px-3 py-1 rounded-md font-semibold">
    Coming Soon
  </div>
  @if($availableTxt)
    <div class="text-ink">Available {{ $availableTxt }}</div>
  @endif
  <p class="text-muted">Sign in or create an account to get notified.</p>
  <div class="flex gap-3">
    <a href="{{ route('login') }}" class="btn-gold">Sign in</a>
    <a href="{{ route('register') }}" class="text-brand underline hover:text-brand-2">Create account</a>
  </div>
</div>
'@
Write-File "resources/views/components/product/coming-soon.blade.php" $comingSoon

$meta = @'
@props(['product'])

<dl class="space-y-1">
  @if(!empty($product->colorway))
    <div class="flex gap-2">
      <dt class="text-muted w-20">Shown:</dt>
      <dd class="text-ink">{{ $product->colorway }}</dd>
    </div>
  @endif
  @if(!empty($product->style_code))
    <div class="flex gap-2">
      <dt class="text-muted w-20">Style:</dt>
      <dd class="text-ink">{{ $product->style_code }}</dd>
    </div>
  @endif
  @if(!empty($product->materials))
    <div class="flex gap-2">
      <dt class="text-muted w-20">Materials:</dt>
      <dd class="text-ink">{{ $product->materials }}</dd>
    </div>
  @endif
</dl>
'@
Write-File "resources/views/components/product/meta.blade.php" $meta

$card = @'
@props(['product'])

@php
  $img = is_array($product->images[0] ?? null)
      ? ($product->images[0]['src'] ?? '')
      : ($product->images[0]->url ?? $product->images[0] ?? '');
@endphp

<a href="{{ route('product.show', $product->slug) }}"
   class="block bg-surface border border-thin rounded-lg overflow-hidden hover:border-brand transition">
  @if($img)
    <div class="aspect-[4/3] bg-bg">
      <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
    </div>
  @endif
  <div class="p-3 space-y-1">
    <h2 class="text-ink font-semibold text-sm truncate">{{ $product->name }}</h2>
    @if($product->category?->name)
      <div class="text-muted text-xs">{{ $product->category->name }}</div>
    @endif
    @if(($product->status ?? null) === 'coming_soon')
      <span class="inline-block bg-brand text-black text-xs px-2 py-0.5 rounded-full font-medium">
        Coming Soon
      </span>
    @endif
  </div>
</a>
'@
Write-File "resources/views/components/product/card.blade.php" $card

# Legacy wrapper (if any includes still reference it)
$legacy = @'
@php $product = $product ?? ($product ?? null); @endphp
<x-product.card :product="$product" />
'@
Write-File "resources/views/components/product-card.blade.php" $legacy

# --- C) Optional view moves: create catalog targets if you later move files ---
# (No destructive moves here—just create folders so you can drop views in.)
if (!(Test-Path "resources/views/catalog")) { New-Item -ItemType Directory "resources/views/catalog" | Out-Null }

# --- D) README quick steps -------------------------------------------------
$readme = @'
Heir Luxury – Update Notes

1) Product detail page now lives at: resources/views/products/show.blade.php
2) New reusable components:
   - resources/views/components/product/card.blade.php
   - resources/views/components/product/info-panel.blade.php
   - resources/views/components/product/coming-soon.blade.php
   - resources/views/components/product/meta.blade.php
   (legacy: resources/views/components/product-card.blade.php forwards to <x-product.card>)

3) Use the card component in any product grid:
   <x-product.card :product="$p" />

4) (Recommended) Consolidate catalog views under:
   resources/views/catalog/{search.blade.php, categories.blade.php, mega.blade.php}

5) Clear caches:
   php artisan view:clear && php artisan route:clear && php artisan config:clear
'@
Write-File "UPDATE-NOTES.txt" $readme

Write-Host "`nDone. Files updated. Now run:"
Write-Host "php artisan view:clear && php artisan route:clear && php artisan config:clear" -ForegroundColor Yellow
