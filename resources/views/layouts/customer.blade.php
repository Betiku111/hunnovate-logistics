<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — Hunnovate Logistics</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @include('partials.vite')
    @stack('head')
</head>
<body
    x-data="{ sidebarOpen: false }"
    class="font-sans antialiased bg-slate-50 text-ink min-h-screen"
>
    {{-- Mobile sidebar backdrop --}}
    <div
        x-show="sidebarOpen"
        x-cloak
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-navy/40 backdrop-blur-sm lg:hidden"
    ></div>

    {{-- Sidebar --}}
    @include('customer.partials.sidebar')

    {{-- Main wrapper --}}
    <div class="lg:pl-64 flex flex-col min-h-screen">
        @include('customer.partials.topbar')

        <main class="flex-1">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-[color:var(--color-border)] bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-xs text-ink-muted flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>© {{ date('Y') }} Hunnovate Logistics. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="#faq" class="hover:text-ink">Help</a>
                    <a href="mailto:support@hunnovate.com" class="hover:text-ink">Support</a>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>