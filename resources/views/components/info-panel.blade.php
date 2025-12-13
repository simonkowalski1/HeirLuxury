{{-- resources/views/components/info-panel.blade.php --}}

@props([
    'title'   => 'Information',
    'message' => null,
])

<div class="rounded-xl border border-white/10 bg-surface px-4 py-6">
    <h2 class="text-base font-semibold text-white mb-1">
        {{ $title }}
    </h2>

    @if ($message)
        <p class="text-sm text-white/70">
            {{ $message }}
        </p>
    @endif

    {{ $slot }}
</div>
