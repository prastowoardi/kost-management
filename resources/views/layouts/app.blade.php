<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased"
        data-flash-success="{{ session('success') ? e(session('success')) : '' }}"
        data-flash-error="{{ session('error') ? e(session('error')) : '' }}">
        <div class="min-h-screen bg-stone-100" x-data="{ menuOpen: false }"
            x-effect="document.body.classList.toggle('overflow-hidden', menuOpen)">
            @if(!isset($hideNavigation))
                @include('layouts.navigation')
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="relative overflow-hidden border-b border-stone-200/70 bg-gradient-to-br from-white via-brand-50/60 to-stone-50">
                    <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-brand-100/50 blur-3xl"></div>
                    <div class="page-container relative py-4 sm:py-5">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
