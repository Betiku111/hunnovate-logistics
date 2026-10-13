<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Hunnovate Logistics — Fast, Reliable Delivery in Nigeria')</title>

    <meta name="description" content="@yield('meta_description', 'Hunnovate Logistics provides same-day, intercity, and business delivery services with live tracking, verified riders, and transparent pricing.')">

    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('og_title', 'Hunnovate Logistics')">
    <meta property="og:description" content="@yield('og_description', 'Fast, reliable, tracked delivery — Motorcycle, Car, and Truck.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/brand/og-default.jpg') }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Vite --}}
    @include('partials.vite')

    @stack('head')
</head>
<body class="font-sans antialiased bg-white text-ink">

    @include('home.partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('home.partials.footer')

    @stack('scripts')
</body>
</html>