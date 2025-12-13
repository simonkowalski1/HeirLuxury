@props([
  'catalog'   => [],     // config('categories')
  'active'    => null,   // ['gender'=>'women|men','section'=>..., 'name'=>..., 'slug'=>...]
  'collapsed' => true,   // default closed; opens active automatically
])

@php
  use Illuminate\Support\Str;

  // Helpers
  $fromHref = fn ($href) => trim(str_replace('/categories/', '', (string) $href), '/');
  $norm     = fn ($slug) => Str::of((string)$slug)->lower()->slug('-')->toString();

  $genders = ['women' => 'Women', 'men' => 'Men'];

  // Current slug for active-state compare
  $currentSlug = $active['slug']
    ?? Str::of(request()->route('slug') ?? '')->lower()->slug('-')->toString();

  // Should each gender start open?
  $openWomen = (($active['gender'] ?? null) === 'women') ? 'true' : 'false';
  $openMen   = (($active['gender'] ?? null) === 'men')   ? 'true' : 'false';

  // Detect if a top-level “All {Gender}” already exists anywhere
  $hasAllTop = ['women' => false, 'men' => false];
  foreach ($genders as $gKey => $gLabel) {
    foreach ((array)($catalog[$gKey] ?? []) as $section => $links) {
      foreach ((array)$links as $item) {
        $raw  = $item['params']['slug'] ?? (isset($item['href']) ? $fromHref($item['href']) : null);
        $slug = $raw ? $norm($raw) : null;
        if ($slug === $gKey) { $hasAllTop[$gKey] = true; break 2; }
      }
    }
  }
@endphp

<nav aria-label="Categories" class="space-y-4">
  @foreach($genders as $gKey => $gLabel)
    <div
      x-data="{
        open: {{ $collapsed ? ($gKey === 'women' ? $openWomen : $openMen) : 'true' }} === 'true',
        centerActive() {
          const c = this.$refs.scroll;
          if (!c) return;
          const a = c.querySelector('.sidenav-link--active');
          if (!a) return;
          const target = a.offsetTop - (c.clientHeight / 2) + (a.offsetHeight / 2);
          try { c.scrollTo({ top: Math.max(0, target), behavior: 'instant' }); }
          catch (_) { c.scrollTop = Math.max(0, target); }
        }
      }"
      x-init="$nextTick(() => { if (open) centerActive() })"
      class="border-b border-thin pb-2"
    >
      <!-- Gender header (collapsible trigger) -->
      <button
        type="button"
        @click="open = !open; $nextTick(() => { if (open) centerActive() })"
        class="w-full flex items-center justify-between text-left text-sm font-semibold text-ink py-2"
        :aria-expanded="open"
      >
        <span class="tracking-tight">{{ $gLabel }}</span>
        <svg class="w-5 h-5 chev" :class="open ? 'chev--open' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.17l3.71-2.94a.75.75 0 111.04 1.08l-4.24 3.36a.75.75 0 01-.94 0L5.25 8.31a.75.75 0 01-.02-1.1z" clip-rule="evenodd" />
        </svg>
      </button>

      <!-- Scrollable body -->
      <div x-show="open" x-collapse x-ref="scroll" class="pl-1 gold-scroll" style="max-height:60vh; overflow-y:auto;">
        <div class="space-y-1 pb-2">
          {{-- All {Gender} (only if not present elsewhere) --}}
          @unless($hasAllTop[$gKey])
            <a href="{{ route('categories.show', $gKey) }}"
               class="sidenav-link {{ $currentSlug === $gKey ? 'sidenav-link--active' : '' }}">
              All {{ $gLabel }}
            </a>
          @endunless

          {{-- Sections --}}
          @foreach((array)($catalog[$gKey] ?? []) as $section => $links)
            @php
              $sectionSlug    = $norm($section);             // e.g. "bags", "shoes"
              $sectionAllSlug = "{$gKey}-{$sectionSlug}";    // e.g. "women-bags", "men-shoes"
              $hasSectionAll  = false;

              // See if config already contains “All {Gender} {Section}”
              foreach ((array)$links as $item) {
                $raw  = $item['params']['slug'] ?? (isset($item['href']) ? $fromHref($item['href']) : null);
                $slug = $raw ? $norm($raw) : null;
                if ($slug === $sectionAllSlug) { $hasSectionAll = true; break; }
              }
            @endphp

            <div class="mt-2 first:mt-0">
              <div class="text-muted text-xs uppercase tracking-wide px-1">{{ $section }}</div>
              <hr class="hr-gold my-1">

              {{-- All {Gender} {Section} --}}
              @unless($hasSectionAll)
                <a href="{{ route('categories.show', $sectionAllSlug) }}"
                   class="sidenav-link {{ $currentSlug === $sectionAllSlug ? 'sidenav-link--active' : '' }}">
                  All {{ $gLabel }} {{ $section }}
                </a>
              @endunless

              {{-- Section items --}}
              @foreach((array)$links as $item)
                @php
                  $raw  = $item['params']['slug'] ?? (isset($item['href']) ? $fromHref($item['href']) : null);
                  $slug = $raw ? $norm($raw) : null;
                  $name = $item['name'] ?? Str::headline($slug ?? '');
                  // Skip duplicate “All {Gender} {Section}” if config already has it
                  if ($slug === $sectionAllSlug) continue;
                  // Skip duplicate “All {Gender}” if it exists
                  if ($slug === $gKey && $hasAllTop[$gKey]) continue;
                @endphp
                @if($slug)
                  <a href="{{ route('categories.show', $slug) }}"
                     class="sidenav-link {{ $currentSlug === $slug ? 'sidenav-link--active' : '' }}">
                    {{ $name }}
                  </a>
                @endif
              @endforeach
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @endforeach
</nav>
