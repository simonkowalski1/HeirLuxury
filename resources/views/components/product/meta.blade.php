@props(['product'])
<dl class="space-y-1">
@if(!empty($product->colorway))
<div class="flex gap-2">
<dt class="text-muted w-20">Shown:</dt>
<dd class="text-ink">{{ $product->colorway }}</dd>
</div>
@endif
@if(!empty($product->style_code))
<div class="flex gap-2">
<dt class="text-muted w-20">Style:</dt>
<dd class="text-ink">{{ $product->style_code }}</dd>
</div>
@endif
@if(!empty($product->materials))
<div class="flex gap-2">
<dt class="text-muted w-20">Materials:</dt>
<dd class="text-ink">{{ $product->materials }}</dd>
</div>
@endif
</dl>