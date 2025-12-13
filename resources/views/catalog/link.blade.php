{{-- resources/views/catalog/product.blade.php --}}
@extends('layouts.app') {{-- or whatever layout your catalog pages use --}}

@section('title', $product->name)

@section('content')
  <main class="max-w-6xl mx-auto px-4 py-10 text-white">
    {{-- Simple breadcrumb --}}
    <nav class="text-xs text-white/60 mb-6">
      <a href="{{ url('/') }}" class="hover:text-amber-300">Home</a>
      <span class="mx-1">/</span>
      <a href="{{ route('catalog.category', $product->category_slug) }}" class="hover:text-amber-300">
        {{ $product->category_slug }}
      </a>
      <span class="mx-1">/</span>
      <span class="text-white">{{ $product->name }}</span>
    </nav>

    <div class="grid md:grid-cols-2 gap-8">
      {{-- Left: main image --}}
      <div class="rounded-2xl overflow-hidden bg-black/40 border border-white/10">
        @php
          use Illuminate\Support\Facades\Storage;
          $imageUrl = $product->image_path ? Storage::url($product->image_path) : asset('assets/placeholders/product-dark.png');
        @endphp

        <img
          src="{{ $imageUrl }}"
          alt="{{ $product->name }}"
          class="w-full h-full object-cover"
        />
      </div>

      {{-- Right: details --}}
      <div>
        <h1 class="text-2xl font-semibold mb-2">{{ $product->name }}</h1>

        @if($product->brand)
          <p class="text-sm text-white/70 mb-1">Brand: {{ $product->brand }}</p>
        @endif

        @if($product->section)
          <p class="text-sm text-white/70 mb-1">Section: {{ $product->section }}</p>
        @endif

        <p class="mt-4 text-sm text-white/70">
          Curated {{ $product->category_slug }} piece.
        </p>
      </div>
    </div>

    {{-- Related items --}}
    @if($related->count())
      <section class="mt-10">
        <h2 class="text-lg font-semibold mb-4">More in this category</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          @foreach($related as $item)
            <x-product.card :product="$item" />
          @endforeach
        </div>
      </section>
    @endif
  </main>
@endsection
