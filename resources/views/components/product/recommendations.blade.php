@props(['items' => []])


<div class="relative" x-data="{el:null}" x-init="el=$refs.track">
<div class="overflow-x-auto gold-scroll" x-ref="track"
@recs-prev.window="el.scrollBy({left:-320,behavior:'smooth'})"
@recs-next.window="el.scrollBy({left:320,behavior:'smooth'})">
<ul class="flex gap-4 pr-2">
@foreach($items as $p)
<li class="w-[240px] shrink-0">
<x-product.card :product="$p" />
</li>
@endforeach
</ul>
</div>
</div>