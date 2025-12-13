@extends('admin.layouts.app')

@section('title', 'New Category')

@section('content')
    <div class="max-w-xl space-y-4">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">Name</label>
                <input name="name"
                       value="{{ old('name') }}"
                       class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400"
                       required>
                @error('name')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">Slug (optional)</label>
                <input name="slug"
                       value="{{ old('slug') }}"
                       class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400">
                <p class="mt-1 text-xs text-zinc-500">
                    Leave empty to auto-generate from the name.
                </p>
                @error('slug')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2 flex items-center gap-2">
                <button class="rounded-full bg-amber-400 text-black px-4 py-2 text-sm font-medium hover:bg-amber-300">
                    Save
                </button>
                <a href="{{ route('admin.categories.index') }}" class="text-sm text-zinc-400 hover:text-zinc-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
