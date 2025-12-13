{{-- Renders a grouped catalog list for the search page / mega-style lists --}}
@php
  use Illuminate\Support\Str;

  $catalog = (array) ($catalog ?? []);
  $fromHref = fn ($href) => trim(str_replace('/categories/', '', (string) $href), '/');
  $norm     = fn ($slug) => Str::of((string) $slug)->lower()->slug('-')->toString();
@endphp

<div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
  @foreach (['women' => 'Women', 'men' => 'Men'] as $gKey => $gLabel)
    @if(isset($catalog[$gKey]) && is_array($catalog[$gKey]))
      <section>
        <h2 class="text-sm text-muted uppercase tracking-wide mb-3">{{ $gLabel }}</h2>

        @foreach ($catalog[$gKey] as $section => $links)
          <div class="mb-4 last:mb-0">
            <div class="text-xs text-muted uppercase tracking-wide mb-1">{{ $section }}</div>
            <hr class="hr-gold my-1">

            <ul class="space-y-1">
              @foreach ((array) $links as $item)
                @php
                  $raw  = $item['params']['slug'] ?? (isset($item['href']) ? $fromHref($item['href']) : null);
                  $slug = $raw ? $norm($raw) : null;
                  $name = $item['name'] ?? Str::headline($slug ?? '');
                @endphp
                @if ($slug)
                  <li>
                    <a href="{{ route('categories.show', $slug) }}" class="sidenav-link">{{ $name }}</a>
                  </li>
                @endif
              @endforeach
            </ul>
          </div>
        @endforeach
      </section>
    @endif
  @endforeach
</div>
