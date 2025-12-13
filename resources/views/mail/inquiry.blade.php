@php($p = $payload)
<h2>New product inquiry</h2>

<p><strong>Product:</strong> {{ $p['product']['name'] ?? 'Unknown' }}</p>
@if(!empty($p['product']['url']))
<p><strong>URL:</strong> {{ $p['product']['url'] }}</p>
@endif

<p><strong>From:</strong> {{ $p['form']['first_name'] }} {{ $p['form']['last_name'] }} ({{ $p['form']['email'] }})</p>
@if(!empty($p['form']['phone']))
<p><strong>Phone:</strong> {{ $p['form']['phone'] }}</p>
@endif

<p><strong>Message:</strong></p>
<p>{{ $p['form']['message'] }}</p>
