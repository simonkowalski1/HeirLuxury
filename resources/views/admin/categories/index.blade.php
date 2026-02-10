@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-lg font-semibold">Categories</h3>
        <a href="{{ route('admin.categories.create') }}"
           class="rounded-full bg-amber-400 text-black px-4 py-2 text-sm font-medium hover:bg-amber-300">
            + New Category
        </a>
    </div>

    @if(session('status'))
        <div class="mb-4 rounded-lg border border-emerald-400/40 bg-emerald-400/10 px-4 py-2 text-sm text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    {{-- Search & Filters --}}
    <div class="mb-6 rounded-2xl border border-white/10 bg-zinc-900/60 p-4">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="flex-1">
                <label for="search" class="sr-only">Search categories</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by name or slug..."
                           class="w-full rounded-lg bg-zinc-800 border border-white/10 pl-10 pr-4 py-2 text-sm text-white placeholder-zinc-500 outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/50 transition-colors">
                </div>
            </div>

            {{-- Has Products Filter --}}
            <div class="sm:w-44">
                <label for="has_products" class="sr-only">Filter by products</label>
                <select id="has_products"
                        name="has_products"
                        class="w-full rounded-lg bg-zinc-800 border border-white/10 px-3 py-2 text-sm text-white outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/50 transition-colors">
                    <option value="">All Categories</option>
                    <option value="1" @selected(request('has_products') === '1')>With Products</option>
                    <option value="0" @selected(request('has_products') === '0')>Empty</option>
                </select>
            </div>

            {{-- Preserve sort params --}}
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('direction'))
                <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif

            {{-- Submit --}}
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-700 text-white px-4 py-2 text-sm font-medium hover:bg-zinc-600 transition-colors border border-white/10">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>

            @if(request()->hasAny(['search', 'has_products']))
                <a href="{{ route('admin.categories.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-800 text-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-700 transition-colors border border-white/10">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear
                </a>
            @endif
        </form>
    </div>

    @php
        // Build sort URL preserving search/filter params
        $sortUrl = function (string $column) use ($sort, $direction) {
            $newDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $newDirection]);
        };

        // Sort icon for column headers
        $sortIcon = function (string $column) use ($sort, $direction) {
            if ($sort !== $column) {
                return '<svg class="h-3 w-3 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>';
            }
            if ($direction === 'asc') {
                return '<svg class="h-3 w-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>';
            }
            return '<svg class="h-3 w-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
        };
    @endphp

    <div class="overflow-x-auto rounded-2xl border border-white/10 bg-zinc-900/60">
        <table class="min-w-full text-sm">
            <thead class="bg-zinc-900">
                <tr class="text-left text-xs uppercase text-zinc-400">
                    <th class="px-4 py-3">
                        <a href="{{ $sortUrl('name') }}" class="inline-flex items-center gap-1 hover:text-white transition-colors">
                            Name {!! $sortIcon('name') !!}
                        </a>
                    </th>
                    <th class="px-4 py-3">
                        <a href="{{ $sortUrl('slug') }}" class="inline-flex items-center gap-1 hover:text-white transition-colors">
                            Slug {!! $sortIcon('slug') !!}
                        </a>
                    </th>
                    <th class="px-4 py-3 w-24 text-center">
                        <a href="{{ $sortUrl('products_count') }}" class="inline-flex items-center gap-1 hover:text-white transition-colors">
                            Products {!! $sortIcon('products_count') !!}
                        </a>
                    </th>
                    <th class="px-4 py-3 w-32 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($categories as $category)
                <tr class="border-t border-white/5 hover:bg-white/5">
                    <td class="px-4 py-3">{{ $category->name }}</td>
                    <td class="px-4 py-3 text-zinc-400">{{ $category->slug }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center justify-center min-w-[24px] rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $category->products_count > 0 ? 'bg-amber-400/20 text-amber-300' : 'bg-zinc-800 text-zinc-500' }}">
                            {{ $category->products_count }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('admin.categories.edit', $category) }}"
                           class="text-xs text-amber-300 hover:text-amber-200">
                            Edit
                        </a>
                        <form action="{{ route('admin.categories.destroy', $category) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-300">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-4 py-6 text-center text-zinc-400" colspan="4">
                        No categories yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
@endsection
