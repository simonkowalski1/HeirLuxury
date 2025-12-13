<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Heir Luxury Admin - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white min-h-screen flex">
    {{-- Sidebar --}}
    <aside class="w-64 bg-zinc-950 border-r border-white/10 p-4 space-y-6">
        <h1 class="text-xl font-semibold tracking-wide">
            Heir Luxury <span class="text-amber-400">Admin</span>
        </h1>

        <nav class="space-y-2 text-sm">
    <a href="{{ route('admin.dashboard') }}"
       class="block px-3 py-2 rounded-lg hover:bg-white/5 @if(request()->routeIs('admin.dashboard')) bg-white/10 @endif">
        Dashboard
    </a>

    <a href="{{ route('admin.products.index') }}"
       class="block px-3 py-2 rounded-lg hover:bg-white/5 @if(request()->routeIs('admin.products.*')) bg-white/10 @endif">
        Products
    </a>

    <a href="{{ route('admin.categories.index') }}"
       class="block px-3 py-2 rounded-lg hover:bg-white/5 @if(request()->routeIs('admin.categories.*')) bg-white/10 @endif">
        Categories
    </a>
</nav>


        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="mt-4 text-xs text-zinc-400 hover:text-white">
                Logout
            </button>
        </form>
    </aside>

    {{-- Main content --}}
    <main class="flex-1 p-6">
        <header class="mb-6">
            <h2 class="text-2xl font-semibold">@yield('title', 'Dashboard')</h2>
            <p class="text-xs text-zinc-400">
                Logged in as {{ auth()->user()->email }}
            </p>
        </header>

        @yield('content')
    </main>
</body>
</html>
