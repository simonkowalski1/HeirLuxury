{{-- resources/views/catalog/categories.blade.php --}}
@extends('layouts.public')

@section('title', $title ?? 'Catalog')

@section('content')
@php
  $locale = app()->getLocale();
  $breadcrumbs = [
    ['label' => 'Home', 'href' => route('home', ['locale' => $locale])],
    ['label' => 'Catalog', 'href' => route('catalog.grouped', ['locale' => $locale])],
    ['label' => $title ?? 'Category', 'href' => null],
  ];
@endphp

<x-breadcrumbs :items="$breadcrumbs" />

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">

  {{-- Left: existing collapsible sidenav --}}
  <aside class="lg:col-span-3">
    <div class="lg:sticky lg:top-24">
      @include('catalog._sidenav', [
        'catalog'    => $catalog ?? null,
        'activeSlug' => $slug ?? null,
      ])
    </div>
  </aside>

  {{-- Right: products with infinite scroll --}}
  <section class="lg:col-span-9 min-w-0"
    x-data="{
      loading: false,
      hasMore: {{ $products->hasMorePages() ? 'true' : 'false' }},
      nextPage: {{ $products->currentPage() + 1 }},
      category: '{{ $slug ?? '' }}',

      async loadMore() {
        if (this.loading || !this.hasMore) return;

        this.loading = true;

        try {
          const url = new URL('{{ route('api.catalog.products') }}', window.location.origin);
          url.searchParams.set('page', this.nextPage);
          if (this.category) {
            url.searchParams.set('category', this.category);
          }

          const response = await fetch(url);
          const data = await response.json();

          if (data.html) {
            this.$refs.productGrid.insertAdjacentHTML('beforeend', data.html);
          }

          this.hasMore = data.hasMore;
          this.nextPage = data.nextPage;
        } catch (error) {
          console.error('Failed to load more products:', error);
        } finally {
          this.loading = false;
        }
      }
    }"
  >
    @if(isset($products) && $products->count())
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6" x-ref="productGrid">
        @foreach($products as $product)
          <div class="h-full">
            <x-product.card :product="$product" />
          </div>
        @endforeach
      </div>

      {{-- Infinite scroll trigger --}}
      <div
        x-show="hasMore"
        x-intersect:enter.margin.200px="loadMore()"
        class="mt-10 flex justify-center"
      >
        <div x-show="loading" class="flex items-center gap-3 text-white/60">
          <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Loading more products...</span>
        </div>
      </div>

      {{-- End of results message --}}
      <div x-show="!hasMore && !loading" class="mt-10 text-center text-white/40 text-sm">
        You've reached the end of the catalog
      </div>

      {{-- Fallback pagination for non-JS users --}}
      <noscript>
        <div class="mt-10">
          {{ $products->links() }}
        </div>
      </noscript>
    @else
      <div class="rounded-2xl border border-white/10 bg-white/5 p-12 text-center">
        <h2 class="text-xl font-medium text-white">No products found</h2>
        <p class="mt-3 text-white/60">This category is currently empty.</p>
      </div>
    @endif
  </section>

</div>
@endsection
