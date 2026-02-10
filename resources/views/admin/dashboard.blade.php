{{-- ABOUTME: Admin dashboard view displaying catalog metrics and recent activity. --}}
{{-- ABOUTME: Renders stat cards, top brands, gender breakdown, and recent products. --}}

@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Summary Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Total Products --}}
        <div class="rounded-2xl border border-white/10 bg-zinc-900/60 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Total Products</p>
                    <p class="mt-1 text-2xl font-bold text-white">{{ number_format($metrics['total_products']) }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-amber-400/10 flex items-center justify-center">
                    <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Categories --}}
        <div class="rounded-2xl border border-white/10 bg-zinc-900/60 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Total Categories</p>
                    <p class="mt-1 text-2xl font-bold text-white">{{ number_format($metrics['total_categories']) }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-amber-400/10 flex items-center justify-center">
                    <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Gender Breakdown --}}
        @foreach(['women' => 'Women', 'men' => 'Men'] as $genderKey => $genderLabel)
            <div class="rounded-2xl border border-white/10 bg-zinc-900/60 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-zinc-400 uppercase tracking-wider">{{ $genderLabel }}</p>
                        <p class="mt-1 text-2xl font-bold text-white">{{ number_format($metrics['products_by_gender'][$genderKey] ?? 0) }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full {{ $genderKey === 'women' ? 'bg-pink-400/10' : 'bg-blue-400/10' }} flex items-center justify-center">
                        <svg class="h-5 w-5 {{ $genderKey === 'women' ? 'text-pink-400' : 'text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Top Brands --}}
        <div class="rounded-2xl border border-white/10 bg-zinc-900/60 p-6">
            <h3 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Top Brands
            </h3>

            @if(!empty($metrics['products_by_brand']))
                @php
                    $maxCount = max($metrics['products_by_brand']);
                @endphp
                <ul class="space-y-3">
                    @foreach($metrics['products_by_brand'] as $brand => $count)
                        <li>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-white">{{ $brand }}</span>
                                <span class="text-xs text-zinc-400">{{ number_format($count) }}</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-zinc-800 overflow-hidden">
                                <div class="h-full rounded-full bg-amber-400" style="width: {{ $maxCount > 0 ? round(($count / $maxCount) * 100) : 0 }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-zinc-500">No brand data available.</p>
            @endif
        </div>

        {{-- Recent Products --}}
        <div class="lg:col-span-2 rounded-2xl border border-white/10 bg-zinc-900/60 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                    <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Recent Products
                </h3>
                @if($recentProducts->isNotEmpty())
                    <a href="{{ route('admin.products.index') }}" class="text-xs text-amber-400 hover:text-amber-300 transition-colors">
                        View all &rarr;
                    </a>
                @endif
            </div>

            @if($recentProducts->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-zinc-500">
                                <th class="pb-3 pr-4">Name</th>
                                <th class="pb-3 pr-4">Brand</th>
                                <th class="pb-3 pr-4">Gender</th>
                                <th class="pb-3 text-right">Added</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($recentProducts as $product)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-2.5 pr-4">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="text-white hover:text-amber-400 transition-colors font-medium">
                                            {{ $product->name }}
                                        </a>
                                    </td>
                                    <td class="py-2.5 pr-4 text-zinc-400">{{ $product->brand ?? '—' }}</td>
                                    <td class="py-2.5 pr-4">
                                        @if($product->gender)
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                {{ $product->gender === 'women' ? 'bg-pink-400/20 text-pink-300' : 'bg-blue-400/20 text-blue-300' }}">
                                                {{ ucfirst($product->gender) }}
                                            </span>
                                        @else
                                            <span class="text-zinc-500">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 text-right text-xs text-zinc-500">
                                        {{ $product->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-10 w-10 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="mt-3 text-sm text-zinc-400">No products yet</p>
                    <a href="{{ route('admin.products.create') }}" class="mt-2 inline-flex items-center gap-1 text-xs text-amber-400 hover:text-amber-300 transition-colors">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create your first product
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
