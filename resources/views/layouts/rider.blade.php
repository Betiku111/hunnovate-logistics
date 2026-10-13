<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Rider') — Hunnovate</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @include('partials.vite')
    @stack('head')
</head>
<body class="font-sans antialiased bg-slate-50 text-ink">
    <div class="min-h-screen flex flex-col max-w-2xl mx-auto bg-white shadow-xl">

        @include('rider.partials.topbar')

        <main class="flex-1 pb-24">
            @yield('content')
        </main>

        @include('rider.partials.bottomnav')
    </div>

    @stack('scripts')
</body>
</html>