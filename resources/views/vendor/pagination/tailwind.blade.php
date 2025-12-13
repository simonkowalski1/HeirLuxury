@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between mt-8">

        {{-- Left: Previous Button --}}
        <div class="flex-1">
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 border border-white/20 text-white/30 rounded-lg">
                    ‹ Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-4 py-2 border border-yellow-400 text-yellow-400 rounded-lg hover:bg-yellow-400 hover:text-black transition">
                    ‹ Previous
                </a>
            @endif
        </div>

        {{-- Center: Page Counter with Editable Input --}}
        <div class="flex-1 flex items-center justify-center gap-2 text-white/70">
            <span>Page</span>

            <form method="GET" class="inline-flex">
                {{-- keep all existing query params except page --}}
                @foreach(request()->except('page') as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach

                <input
                    type="number"
                    name="page"
                    value="{{ $paginator->currentPage() }}"
                    min="1"
                    max="{{ $paginator->lastPage() }}"
                    class="w-16 text-center bg-transparent border border-white/30 rounded-lg
                           focus:border-yellow-400 focus:outline-none focus:ring-0"
                    onchange="this.form.submit()"
                >
            </form>

            <span>of {{ $paginator->lastPage() }}</span>
        </div>

        {{-- Right: Next Button --}}
        <div class="flex-1 text-right">
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-4 py-2 bg-yellow-400 text-black rounded-lg hover:bg-yellow-500 transition">
                    Next ›
                </a>
            @else
                <span class="px-4 py-2 bg-white/10 text-white/30 rounded-lg">
                    Next ›
                </span>
            @endif
        </div>

    </nav>
@endif
