@extends('layouts.public')

@section('content')
<div class="relative min-h-[calc(100vh-4rem)] flex items-center justify-center px-6 py-16">

    {{-- Decorative Background Glow --}}
    <div class="pointer-events-none absolute inset-0 opacity-40"
         style="background: radial-gradient(circle at 0% 0%, rgba(234,179,8,0.4), transparent 55%),
                          radial-gradient(circle at 100% 0%, rgba(250,204,21,0.35), transparent 55%);">
    </div>

    {{-- LUXURY HERO PANEL --}}
    <section
        class="relative w-full max-w-6xl mx-auto overflow-hidden rounded-3xl border border-white/10
               bg-gradient-to-r from-slate-900 via-slate-900/95 to-slate-900/80
               px-10 py-14 sm:px-14 sm:py-16
               shadow-[0_40px_120px_rgba(0,0,0,0.85)] backdrop-blur-xl">

        {{-- MAIN HERO CONTENT (CENTERED) --}}
        <div class="relative max-w-3xl mx-auto text-center space-y-6">

            <p class="text-xs font-medium tracking-[0.38em] text-yellow-300 uppercase">
                Collection
            </p>

            <h1 class="text-4xl sm:text-5xl md:text-6xl font-semibold leading-tight text-slate-50">
                Curated Designer Pieces
                <span class="block text-yellow-300">Verified Quality</span>
            </h1>

            <p class="max-w-xl mx-auto text-sm sm:text-base text-slate-300/80">
                Explore a hand-picked selection of premium accessories and apparel,
                authenticated and styled for modern collectors.
            </p>

            <div class="flex flex-wrap justify-center items-center gap-4 pt-2">
                <a href="{{ route('catalog.grouped') }}"
                   class="inline-flex items-center rounded-full bg-yellow-400 px-6 py-2.5 text-sm font-medium
                          text-black shadow-sm hover:bg-yellow-300 transition-colors">
                    New
                </a>

                <a href="{{ route('catalog.grouped') }}"
                   class="inline-flex items-center rounded-full border border-white/20 px-6 py-2.5
                          text-sm font-medium text-slate-100 hover:border-yellow-300 hover:text-yellow-300
                          transition-colors">
                    Collections
                </a>
            </div>
        </div>

        {{-- ENTABLATURE / FRIEZE DIVIDER --}}
        <div class="relative mt-10">
            {{-- main band --}}
            <div class="mx-auto h-px max-w-4xl bg-gradient-to-r from-transparent via-yellow-400/80 to-transparent"></div>

            {{-- small decorative blocks in the center --}}
            <div class="absolute inset-x-0 -top-1 flex justify-center gap-1">
                <span class="h-2 w-10 rounded-full bg-yellow-500/90"></span>
                <span class="h-2 w-4 rounded-full bg-yellow-300/90"></span>
                <span class="h-2 w-10 rounded-full bg-yellow-500/90"></span>
            </div>
        </div>

                {{-- NEW ARRIVALS CARD (MOVED BELOW HERO) --}}
        <div class="mt-10 flex justify-center">
            @php
  $newArrivals = [
    [
      'label'  => 'Louis Vuitton Women Bags',
      'route'  => 'catalog.category',
      'params' => ['categorySlug' => 'louis-vuitton-women-bags'],
    ],
    [
      'label'  => 'Louis Vuitton Women Shoes',
      'route'  => 'catalog.category',
      'params' => ['categorySlug' => 'louis-vuitton-women-shoes'],
    ],
  ];
@endphp



            <div class="w-full max-w-lg rounded-2xl bg-white/5 border border-white/10 px-6 py-5 backdrop-blur">
                <p class="text-xs font-medium tracking-[0.3em] text-slate-300 uppercase mb-3">
                    New Arrivals
                </p>

                <ul class="space-y-2 text-sm text-slate-200/90">
                    @foreach($newArrivals as $item)
                        <li class="flex justify-between">
                            <span>{{ $item['label'] }}</span>

                            <a href="{{ route($item['route'], $item['params']) }}"
                               class="text-xs text-yellow-300 hover:text-yellow-200 underline-offset-2 hover:underline">
                                View
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
@endsection
