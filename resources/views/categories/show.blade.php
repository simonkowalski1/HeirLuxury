{{-- resources/views/categories/show.blade.php (or similar) --}}
@extends('layouts.public')

@section('title', $title ?? ($category->name ?? 'Category'))

@section('content')

    {{-- Breadcrumbs --}}
    <x-breadcrumbs :items="$breadcrumbs" />

    <div class="mx-auto max-w-7xl px-4 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- Sidenav --}}
            <aside class="lg:col-span-3">
                @include('catalog._sidenav', [
                    'catalog'    => config('categories'),
                    'activeSlug' => $category->slug ?? ($slug ?? null),
                ])
            </aside>

            {{-- Product grid --}}
            <section class="lg:col-span-9">
                <h1 class="text-2xl font-semibold text-white mb-4">
                    {{ $title ?? ($category->name ?? 'Category') }}
                </h1>

                @if ($products->count())
                    {{-- 1 col on very small, 2 on small, 3 on md+ --}}
                    <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
                        @foreach ($products as $product)
                            <div class="h-full">
                                <x-product.card :product="$product" />
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination bar --}}
                    <div class="mt-10 flex items-center justify-between">

                        {{-- Previous --}}
                        @if ($products->onFirstPage())
                            <span class="px-4 py-2 rounded-lg bg-white/5 text-white/40 cursor-not-allowed">
                                ‹ Previous
                            </span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}"
                               class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition">
                                ‹ Previous
                            </a>
                        @endif

                        {{-- Page Indicator --}}
                        <div class="flex items-center gap-2 text-white/70">
                            <span>Page</span>
                            <span class="px-3 py-1 rounded-lg border border-white/20">
                                {{ $products->currentPage() }}
                            </span>
                            <span>of {{ $products->lastPage() }}</span>
                        </div>

                        {{-- Next --}}
@if ($products->currentPage() < $products->lastPage())
    <a href="{{ $products->nextPageUrl() }}"
       class="px-4 py-2 rounded-lg bg-amber-400 hover:bg-amber-300 text-black font-medium transition">
        Next ›
    </a>
@else
    <span class="px-4 py-2 rounded-lg bg-white/5 text-white/40 cursor-not-allowed">
        Next ›
    </span>
@endif

                    </div>

                @else
                    <x-info-panel
                        title="No items yet"
                        message="We’re curating pieces for this category. Check back soon." />
                @endif
            </section>

        </div>
    </div>

    {{-- GA4/GTM: view_category event --}}
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'view_category',
            category_name: @json($title ?? ($category->name ?? 'Category')),
            category_slug: @json($category->slug ?? ($slug ?? null)),
        });
    </script>
@endsection
