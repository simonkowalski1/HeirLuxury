@extends('layouts.public')

@section('title', $product->name)

@section('content')
    @php
        use Illuminate\Support\Facades\Storage;

        $images = [];

        if ($product->image_path) {
            $images[] = [
                'src' => Storage::url($product->image_path),
                'alt' => $product->name,
            ];
        }

        // Later we can auto-scan the folder for 0001.jpg, 0002.jpg, etc.
    @endphp

    {{-- Breadcrumbs --}}
    @if (!empty($breadcrumbs ?? null))
        <x-breadcrumbs :items="$breadcrumbs" />
    @endif

    <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-2 gap-10">

        {{-- LEFT: PRODUCT IMAGE GALLERY --}}
        <div>
            <x-product.gallery :images="$images" class="max-w-xl" />
        </div>

        {{-- RIGHT: PRODUCT DETAILS --}}
        <div class="space-y-6 text-white">
            <h1 class="text-3xl font-bold">{{ $product->name }}</h1>

            <p class="text-white/70 text-sm">
                <strong>Brand:</strong> {{ $product->brand ?? 'Louis Vuitton' }}<br>
                <strong>Section:</strong> {{ $product->section ?? Str::headline($product->category_slug ?? 'Bags') }}
            </p>

            <p class="text-white/60 leading-relaxed max-w-md">
                Placeholder copy for product description. This curated luxury item is part of our selected
                {{ $product->category_slug }} collection. Contact us to inquire about availability or condition.
            </p>

            <button
                type="button"
                x-data
                @click="$dispatch('open-contact-modal')"
                class="inline-flex items-center rounded-full bg-amber-400 text-black px-5 py-2 text-sm font-medium hover:bg-amber-300 transition">
                Contact Us
            </button>
        </div>
    </div>

    {{-- RELATED ITEMS --}}
    @if($related->count())
        <section class="max-w-7xl mx-auto px-4 pb-10 mt-16 text-white">
            <h2 class="text-xl font-semibold mb-4">More in this category</h2>

            <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
                @foreach($related as $item)
                    <div class="h-full">
                        <x-product.card :product="$item" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
