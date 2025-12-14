{{-- resources/views/catalog/categories.blade.php --}}
@extends('layouts.public')

@section('title', $title ?? 'Catalog')

@section('content')
@php
  $breadcrumbs = [
    ['label' => 'Home', 'href' => route('home')],
    ['label' => 'Catalog', 'href' => route('catalog.grouped')],
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

  {{-- Right: products --}}
<section class="lg:col-span-9 min-w-0">
@if(isset($products) && $products->count())

  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($products as $product)
      <div class="h-full">
        <x-product.card :product="$product" />
      </div>
    @endforeach
  </div>

  <div class="mt-10">
    {{ $products->links() }}
  </div>
@else
  <div class="rounded-2xl border border-white/10 bg-white/5 p-12 text-center">
    <h2 class="text-xl font-medium text-white">No products found</h2>
    <p class="mt-3 text-white/60">This category is currently empty.</p>
  </div>
@endif
</section>

</div>
@endsection
