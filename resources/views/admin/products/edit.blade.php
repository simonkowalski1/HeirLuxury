@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-zinc-400">
            <a href="{{ route('admin.products.index') }}" class="hover:text-white transition-colors">Products</a>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white truncate max-w-xs">{{ $product->name }}</span>
        </nav>
    </div>

    <form method="POST"
          action="{{ route('admin.products.update', $product) }}"
          enctype="multipart/form-data">
        @method('PUT')
        @include('admin.products._form')

        <div class="mt-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-full bg-amber-400 text-black px-6 py-2.5 text-sm font-medium hover:bg-amber-300 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Product
                </button>
                <a href="{{ route('admin.products.index') }}"
                   class="inline-flex items-center gap-2 rounded-full bg-zinc-800 text-white px-6 py-2.5 text-sm font-medium hover:bg-zinc-700 transition-colors border border-white/10">
                    Cancel
                </a>
            </div>

            {{-- Delete Button --}}
            <div x-data="{ showDeleteModal: false }">
                <button type="button"
                        @click="showDeleteModal = true"
                        class="inline-flex items-center gap-2 rounded-full bg-red-500/10 text-red-400 px-4 py-2.5 text-sm font-medium hover:bg-red-500/20 transition-colors border border-red-500/20">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </button>

                {{-- Delete Confirmation Modal --}}
                <div x-show="showDeleteModal"
                     x-cloak
                     class="fixed inset-0 z-50 overflow-y-auto"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex min-h-screen items-center justify-center p-4">
                        {{-- Backdrop --}}
                        <div class="fixed inset-0 bg-black/70" @click="showDeleteModal = false"></div>

                        {{-- Modal --}}
                        <div class="relative rounded-2xl bg-zinc-900 border border-white/10 p-6 w-full max-w-md shadow-xl"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="h-10 w-10 rounded-full bg-red-500/20 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">Delete Product</h3>
                                    <p class="text-sm text-zinc-400">This action cannot be undone.</p>
                                </div>
                            </div>

                            <p class="text-sm text-zinc-300 mb-6">
                                Are you sure you want to delete <strong class="text-white">{{ $product->name }}</strong>?
                            </p>

                            <div class="flex items-center justify-end gap-3">
                                <button type="button"
                                        @click="showDeleteModal = false"
                                        class="rounded-full bg-zinc-800 text-white px-4 py-2 text-sm font-medium hover:bg-zinc-700 transition-colors border border-white/10">
                                    Cancel
                                </button>
                                <form action="{{ route('admin.products.destroy', $product) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-full bg-red-500 text-white px-4 py-2 text-sm font-medium hover:bg-red-600 transition-colors">
                                        Delete Product
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
