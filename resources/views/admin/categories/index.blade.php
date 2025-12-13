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

    <div class="overflow-x-auto rounded-2xl border border-white/10 bg-zinc-900/60">
        <table class="min-w-full text-sm">
            <thead class="bg-zinc-900">
                <tr class="text-left text-xs uppercase text-zinc-400">
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3 w-32 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($categories as $category)
                <tr class="border-t border-white/5 hover:bg-white/5">
                    <td class="px-4 py-3">{{ $category->name }}</td>
                    <td class="px-4 py-3 text-zinc-400">{{ $category->slug }}</td>
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
                    <td class="px-4 py-6 text-center text-zinc-400" colspan="3">
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
