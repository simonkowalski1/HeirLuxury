@extends('admin.layouts.app')

@section('title', 'New Product')

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-zinc-400">
            <a href="{{ route('admin.products.index') }}" class="hover:text-white transition-colors">Products</a>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">New Product</span>
        </nav>
    </div>

    <form method="POST"
          action="{{ route('admin.products.store') }}"
          enctype="multipart/form-data">
        @php($product = $product ?? null)
        @include('admin.products._form')

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-amber-400 text-black px-6 py-2.5 text-sm font-medium hover:bg-amber-300 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Create Product
            </button>
            <a href="{{ route('admin.products.index') }}"
               class="inline-flex items-center gap-2 rounded-full bg-zinc-800 text-white px-6 py-2.5 text-sm font-medium hover:bg-zinc-700 transition-colors border border-white/10">
                Cancel
            </a>
        </div>
    </form>
@endsection
