<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Terjadi Kendala') | {{ config('app.name', 'Serrata Kost') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('serrata.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('serrata.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('serrata.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        /* Fallback CSS jika asset vite belum tercompile / gagal dimuat */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1c1917;
            background-color: #fafaf9;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 1rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #ffffff !important;
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px 0 rgba(13, 148, 136, 0.25);
            cursor: pointer;
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px 0 rgba(13, 148, 136, 0.35);
        }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 1rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #44403c !important;
            background: #ffffff;
            border: 1px solid #e7e5e4;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-secondary:hover {
            background: #f5f5f4;
            color: #1c1917 !important;
            border-color: #d6d3d1;
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-stone-50 text-stone-800">
    <div class="relative flex min-h-screen flex-col items-center justify-between overflow-hidden bg-gradient-to-b from-white via-stone-50 to-stone-100 px-4 py-8 sm:px-6 lg:px-8">
        <!-- Background Ambient Glow -->
        @php
            $accentBg = match((string)View::yieldContent('code', '404')) {
                '500' => 'from-rose-200/40 via-amber-200/20 to-teal-200/20',
                '403' => 'from-rose-200/40 via-brand-200/20 to-stone-200/30',
                '419', '429', '401' => 'from-amber-200/40 via-brand-200/20 to-stone-200/30',
                '503' => 'from-indigo-200/40 via-brand-200/20 to-teal-200/30',
                default => 'from-brand-200/40 via-teal-200/20 to-stone-200/30',
            };
        @endphp
        <div class="pointer-events-none absolute -left-28 -top-28 h-96 w-96 rounded-full bg-gradient-to-br {{ $accentBg }} blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-28 -right-28 h-96 w-96 rounded-full bg-brand-200/30 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/2 top-1/3 -translate-x-1/2 h-72 w-72 rounded-full bg-teal-100/20 blur-3xl"></div>

        <!-- Top Navigation / Logo Header -->
        <header class="relative z-10 w-full max-w-4xl flex items-center justify-center py-2">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group transition-transform hover:scale-105 duration-200">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 shadow-soft overflow-hidden">
                    <img src="{{ asset('serrata.png') }}" alt="Serrata Kost" class="h-full w-full object-cover" onerror="this.parentElement.innerHTML='<svg class=\'h-6 w-6 text-white\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z\'/></svg>'">
                </span>
                <div class="text-left">
                    <span class="block text-xl font-extrabold tracking-tight text-stone-900 leading-tight">
                        Serrata <span class="text-brand-600">Kost</span>
                    </span>
                    <span class="block text-[11px] font-medium tracking-wide text-stone-500 uppercase">Management System</span>
                </div>
            </a>
        </header>

        <!-- Main Content Card -->
        <main class="relative z-10 w-full max-w-xl my-auto py-8">
            <div class="relative rounded-3xl border border-stone-200/80 bg-white/90 p-8 sm:p-12 text-center shadow-card backdrop-blur-md">
                
                <!-- Badge Pill -->
                <div class="mb-6">
                    @yield('badge')
                </div>

                <!-- Visual Graphic / Illustration -->
                <div class="relative mx-auto mb-6 flex items-center justify-center select-none">
                    @hasSection('illustration')
                        @yield('illustration')
                    @else
                        <div class="text-[96px] sm:text-[116px] font-black tracking-tighter leading-none text-transparent bg-clip-text bg-gradient-to-r from-brand-600 via-teal-500 to-emerald-400">
                            @yield('code', 'Error')
                        </div>
                    @endif
                </div>

                <!-- Typography -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight mb-3">
                    @yield('heading', View::yieldContent('title', 'Terjadi Kendala'))
                </h1>
                <p class="text-sm sm:text-base text-stone-600 leading-relaxed max-w-md mx-auto mb-8">
                    @yield('description', View::yieldContent('message', 'Terjadi kendala saat memproses permintaan Anda.'))
                </p>

                <!-- Optional Details (e.g. Debug / Notes) -->
                @yield('details')

                <!-- Help / Support Footer -->
                @hasSection('help')
                    @yield('help')
                @else
                    <div class="mt-8 pt-6 border-t border-stone-100 text-xs text-stone-500 flex items-center justify-center gap-1.5">
                        <svg class="h-4 w-4 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Butuh bantuan? Silakan hubungi pengelola atau admin Serrata Kost.</span>
                    </div>
                @endif

            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full max-w-4xl text-center py-4 text-xs text-stone-400">
            &copy; {{ date('Y') }} Serrata Kost.
        </footer>
    </div>
</body>
</html>
