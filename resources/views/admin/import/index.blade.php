@extends('admin.layouts.app')

@section('title', 'Import Products')

@section('content')
    <div class="mb-6">
        <h3 class="text-lg font-semibold">Bulk Import</h3>
        <p class="text-sm text-zinc-400">Import products from folder structure into the database</p>
    </div>

    {{-- Status Messages --}}
    @if(session('status'))
        <div class="mb-4 rounded-lg border border-emerald-400/40 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200 flex items-center gap-2">
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    {{-- Import Output --}}
    @if(session('import_output'))
        <div class="mb-6 rounded-2xl border border-white/10 bg-zinc-900/60 p-4">
            <h4 class="text-sm font-medium text-white mb-2">Import Output</h4>
            <pre class="text-xs text-zinc-400 whitespace-pre-wrap overflow-x-auto max-h-64 overflow-y-auto">{{ session('import_output') }}</pre>
        </div>
    @endif

    {{-- Current State --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <div class="rounded-2xl border border-white/10 bg-zinc-900/60 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Products in Database</p>
                    <p class="mt-1 text-2xl font-bold text-white">{{ number_format($productCount) }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-amber-400/10 flex items-center justify-center">
                    <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-zinc-900/60 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Import Folders</p>
                    <p class="mt-1 text-2xl font-bold text-white">{{ number_format($folderCount) }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-amber-400/10 flex items-center justify-center">
                    <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Form --}}
    <div class="rounded-2xl border border-white/10 bg-zinc-900/60 p-6">
        <h4 class="text-sm font-semibold text-white mb-4">Run Import</h4>
        <p class="text-sm text-zinc-400 mb-6">
            Scans <code class="text-amber-400/80">storage/app/public/imports/</code> for product folders
            organized as <code class="text-amber-400/80">{brand}-{section}-{gender}/Product Name/</code>
        </p>

        <form method="POST"
              action="{{ route('admin.import.run') }}"
              onsubmit="return confirm('Are you sure you want to run the import?');">
            @csrf

            <div class="space-y-4 mb-6">
                {{-- Fresh Import Toggle --}}
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox"
                           name="fresh"
                           value="1"
                           class="mt-0.5 rounded border-white/20 bg-zinc-800 text-amber-400 focus:ring-amber-400/50">
                    <div>
                        <span class="text-sm font-medium text-white">Fresh import</span>
                        <p class="text-xs text-zinc-500">Delete all existing products before importing. Use when re-scraping from scratch.</p>
                    </div>
                </label>

                {{-- Skip Thumbnails Toggle --}}
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox"
                           name="skip_thumbnails"
                           value="1"
                           class="mt-0.5 rounded border-white/20 bg-zinc-800 text-amber-400 focus:ring-amber-400/50">
                    <div>
                        <span class="text-sm font-medium text-white">Skip thumbnails</span>
                        <p class="text-xs text-zinc-500">Skip WebP thumbnail generation during import. Faster, but product cards may lack optimized images.</p>
                    </div>
                </label>
            </div>

            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-amber-400 text-black px-6 py-2.5 text-sm font-medium hover:bg-amber-300 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Run Import
            </button>
        </form>
    </div>
@endsection
