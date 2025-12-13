@extends('layouts.app')

@section('title', $product->name ?? 'Product')


@section('content')
  <div class="mx-auto max-w-7xl px-4 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

      {{-- Optional sidenav, if you want the same layout --}}
      <aside class="lg:col-span-3">
        @include('categories._sidenav', [
          'catalog' => $catalog ?? [],
          'active'  => $active ?? null,
          'slug'    => $slug ?? null,
        ])
      </aside>

      {{-- Product detail --}}
      <section class="lg:col-span-9">

        {{-- BREADCRUMB: Home > Categories > Women/Men > Brand > Product --}}
        @if(!empty($breadcrumbs ?? null))
          <nav class="mb-4 text-xs text-white/60">
            <ol class="flex flex-wrap items-center gap-x-1 gap-y-2">
              @foreach ($breadcrumbs as $crumb)
                <li class="flex items-center">
                  @if(!empty($crumb['url']) && ! $loop->last)
                    <a href="{{ $crumb['url'] }}" class="hover:text-white">
                      {{ $crumb['label'] }}
                    </a>
                    <span class="mx-1 text-white/30">/</span>
                  @else
                    <span class="text-white">
                      {{ $crumb['label'] }}
                    </span>
                  @endif
                </li>
              @endforeach
            </ol>
          </nav>
        @endif

        <h1 class="text-2xl font-semibold text-white mb-4">
          {{ $product->name }}
        </h1>

        @if(View::exists('components.product.gallery'))
          <x-product.gallery :product="$product" />
        @endif

        @if(View::exists('components.product.info-panel'))
          <x-product.info-panel :product="$product" />
        @else
          <div class="text-white/80">
            <p class="mb-2">Brand: {{ $product->brand ?? 'Heir Luxury' }}</p>
            @if(isset($product->price))
              <p class="mb-2">Price: ${{ number_format($product->price, 2) }}</p>
            @endif
            <p class="mt-4 text-sm text-white/60">
              {{ $product->description ?? 'Details coming soon.' }}
            </p>
          </div>
        @endif

        @if(View::exists('components.product.recommendations') && !empty($related ?? null))
          <x-product.recommendations :products="$related" />
        @endif
      </section>

    </div>
  </div>

  {{-- GA4/GTM: view_product event --}}
  <script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      event: 'view_product',
      product_id: @json($product->id),
      product_name: @json($product->name),
      product_brand: @json($product->brand ?? 'Heir Luxury'),
      product_category: @json(optional($product->category)->name),
      product_price: @json($product->price ?? 0)
    });
  </script>
@endsection
