@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-lg font-semibold">Products</h3>
        <a href="{{ route('admin.products.create') }}"
           class="rounded-full bg-amber-400 text-black px-4 py-2 text-sm font-medium hover:bg-amber-300">
            + New Product
        </a>
    </div>

    @if(session('status'))
        <div class="mb-4 rounded-lg border border-emerald-400/40 bg-emerald-400/10 px-4 py-2 text-sm text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-2xl border border-white/10 bg-zinc-900/60">
        <table class="min-w-full text-sm">
            <thead class="bg-zinc-900">
            <tr class="text-left text-xs uppercase text-zinc-400">
                <th class="px-4 py-3">Image</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Brand</th>
                <th class="px-4 py-3">Gender</th>
                <th class="px-4 py-3 w-32 text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr class="border-t border-white/5 hover:bg-white/5">
                    <td class="px-4 py-3">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}"
                                 alt=""
                                 class="h-12 w-12 rounded-lg object-cover">
                        @else
                            <div class="h-12 w-12 rounded-lg bg-zinc-800 flex items-center justify-center text-xs text-zinc-500">
                                N/A
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $product->name }}</div>
                        <div class="text-xs text-zinc-400">{{ $product->slug }}</div>
                    </td>
                    <td class="px-4 py-3 text-zinc-300">
                        {{ optional($product->category)->name ?? $product->category_slug ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-zinc-300">{{ $product->brand ?? '—' }}</td>
                    <td class="px-4 py-3 text-zinc-300">{{ $product->gender ?? '—' }}</td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="text-xs text-amber-300 hover:text-amber-200">
                            Edit
                        </a>
                        <form action="{{ route('admin.products.destroy', $product) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Delete this product?');">
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
                    <td class="px-4 py-6 text-center text-zinc-400" colspan="6">
                        No products yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endsection
