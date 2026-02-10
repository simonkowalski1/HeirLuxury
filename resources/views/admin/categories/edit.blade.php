@extends('admin.layouts.app')

@section('title', 'Edit Category')

@section('content')
    <div class="max-w-xl space-y-4">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">Name</label>
                <input name="name"
                       value="{{ old('name', $category->name) }}"
                       class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400"
                       required>
                @error('name')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">Slug (optional)</label>
                <input name="slug"
                       value="{{ old('slug', $category->slug) }}"
                       class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400">
                <p class="mt-1 text-xs text-zinc-500">
                    Leave empty to auto-generate from the name.
                </p>
                @error('slug')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Gender</label>
                    <select name="gender"
                            class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400">
                        <option value="">— None —</option>
                        <option value="women" @selected(old('gender', $category->gender) === 'women')>Women</option>
                        <option value="men" @selected(old('gender', $category->gender) === 'men')>Men</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Section</label>
                    <input name="section"
                           value="{{ old('section', $category->section) }}"
                           placeholder="e.g. bags, shoes, clothing"
                           class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Brand</label>
                    <input name="brand"
                           value="{{ old('brand', $category->brand) }}"
                           placeholder="e.g. Louis Vuitton"
                           class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Display Order</label>
                    <input name="display_order"
                           type="number"
                           value="{{ old('display_order', $category->display_order) }}"
                           min="0"
                           class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400">
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           @checked(old('is_active', $category->is_active))
                           class="rounded border-white/20 bg-zinc-800 text-amber-400 focus:ring-amber-400/50">
                    <span class="text-sm text-white">Active in navigation</span>
                </label>
            </div>

            <div class="pt-2 flex items-center gap-2">
                <button class="rounded-full bg-amber-400 text-black px-4 py-2 text-sm font-medium hover:bg-amber-300">
                    Update
                </button>
                <a href="{{ route('admin.categories.index') }}" class="text-sm text-zinc-400 hover:text-zinc-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
