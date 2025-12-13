{{-- resources/views/layouts/public.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'HeirLuxury')</title>

    {{-- Tailwind + app.js via Vite (Breeze style) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine for navbar + mega menu --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-50 antialiased">

    @include('layouts.navbar')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @yield('content')
    </main>

    {{-- contact modal --}}
    @includeIf('contact.modal')

</body>
</html>
