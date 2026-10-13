<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — Hunnovate Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @include('partials.vite')
    @stack('head')
</head>
<body
    x-data="{ sidebarOpen: false }"
    class="font-sans antialiased bg-slate-100 text-ink min-h-screen"
>
    {{-- Mobile backdrop --}}
    <div
        x-show="sidebarOpen"
        x-cloak
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-navy/50 backdrop-blur-sm lg:hidden"
    ></div>

    @include('admin.partials.sidebar')

    <div class="lg:pl-60 min-h-screen flex flex-col">
        @include('admin.partials.topbar')

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-[color:var(--color-border)] bg-white px-4 py-3">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-ink-muted">
                <p>© {{ date('Y') }} Hunnovate Logistics · Admin Panel v1.0</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" target="_blank" class="hover:text-ink">View Site</a>
                    <a href="mailto:support@hunnovate.com" class="hover:text-ink">Support</a>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>