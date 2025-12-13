@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-medium text-zinc-400 mb-1">Name</label>
        <input name="name"
               value="{{ old('name', $product->name ?? '') }}"
               class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400"
               required>
        @error('name')
        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-medium text-zinc-400 mb-1">Slug (optional)</label>
        <input name="slug"
               value="{{ old('slug', $product->slug ?? '') }}"
               class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400">
        <p class="mt-1 text-xs text-zinc-500">
            Leave empty to auto-generate from the name.
        </p>
        @error('slug')
        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-medium text-zinc-400 mb-1">Category</label>
        <select name="category_slug"
                class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400">
            <option value="">— None —</option>
            @foreach($categories as $category)
                <option value="{{ $category->slug }}"
                    @selected(old('category_slug', $product->category_slug ?? '') === $category->slug)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_slug')
        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-medium text-zinc-400 mb-1">Brand</label>
        <input name="brand"
               value="{{ old('brand', $product->brand ?? '') }}"
               class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400">
        @error('brand')
        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-medium text-zinc-400 mb-1">Gender</label>
        <input name="gender"
               value="{{ old('gender', $product->gender ?? '') }}"
               class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400">
        @error('gender')
        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-medium text-zinc-400 mb-1">Section</label>
        <input name="section"
               value="{{ old('section', $product->section ?? '') }}"
               class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400">
        @error('section')
        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-medium text-zinc-400 mb-1">Folder</label>
        <input name="folder"
               value="{{ old('folder', $product->folder ?? '') }}"
               class="w-full rounded-lg bg-zinc-900 border border-white/10 px-3 py-2 text-sm outline-none focus:border-amber-400">
        @error('folder')
        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-medium text-zinc-400 mb-1">Image</label>
        <input type="file" name="image"
               class="block w-full text-sm text-zinc-200
                      file:mr-4 file:rounded-full file:border-0
                      file:bg-amber-400 file:px-4 file:py-2 file:text-sm file:font-semibold
                      file:text-black hover:file:bg-amber-300">
        @if(!empty($product->image))
            <p class="mt-2 text-xs text-zinc-400">
                Current: <span class="underline">{{ $product->image }}</span>
            </p>
        @endif
        @error('image')
        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
