@props(['availableTxt' => null])
<div class="bg-surface border border-thin rounded-lg p-4 space-y-2">
<div class="inline-block bg-brand text-black px-3 py-1 rounded-md font-semibold">Coming Soon</div>
@if($availableTxt)
<div class="text-ink">Available {{ $availableTxt }}</div>
@endif
<p class="text-muted">Sign in or create an account to get notified.</p>
<div class="flex gap-3">
<a href="{{ route('login') }}" class="btn-gold">Sign in</a>
<a href="{{ route('register') }}" class="text-brand underline hover:text-brand-2">Create account</a>
</div>
</div>