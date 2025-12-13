@extends('admin.layouts.app')

@section('title', 'New Product')

@section('content')
    <div class="max-w-3xl space-y-4">
        <form method="POST"
              action="{{ route('admin.products.store') }}"
              enctype="multipart/form-data"
              class="space-y-4">
            @php($product = $product ?? null)
            @include('admin.products._form')

            <div class="pt-2 flex items-center gap-2">
                <button class="rounded-full bg-amber-400 text-black px-4 py-2 text-sm font-medium hover:bg-amber-300">
                    Save
                </button>
                <a href="{{ route('admin.products.index') }}" class="text-sm text-zinc-400 hover:text-zinc-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
