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
    </head>
    <body class="font-sans text-stone-900 antialiased">
        <div class="relative flex min-h-screen flex-col items-center overflow-hidden bg-gradient-to-b from-white via-brand-50/40 to-stone-100 px-4 py-10 sm:justify-center">
            <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-brand-100/60 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-brand-50 blur-3xl"></div>

            <!-- Brand -->
            <a href="/" class="relative mb-8 flex items-center gap-2.5">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 shadow-soft">
                    <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                </span>
                <span class="text-2xl font-extrabold tracking-tight text-stone-900">
                    Serrata <span class="text-brand-600">Kost</span>
                </span>
            </a>

            <div class="relative w-full max-w-md rounded-3xl border border-stone-100 bg-white px-6 py-8 shadow-card sm:px-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
