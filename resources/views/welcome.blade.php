<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('serrata.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('serrata.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('serrata.png') }}">

        <title>Serrata Kost - Chill & Comfort Living</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/css/welcome.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-[#f8fafc] dark:bg-[#0f172a] text-[#1e293b] antialiased">

        <nav id="navbar" class="fixed top-0 inset-x-0 z-50 px-4 py-4">
            <div id="navbar-bg" class="mx-auto flex h-16 max-w-6xl items-center justify-between rounded-2xl">
                <div id="logo-text" class="flex items-center gap-2 text-xl font-extrabold tracking-tight">
                    <svg class="h-7 w-7 text-pink-500 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"></path>
                    </svg>
                    <span>Serrata</span><span class="text-pink-500">.</span>
                </div>
                <div id="menu-text" class="font-bold">
                    <a href="#fasilitas" class="text-sm">Fasilitas</a>
                    <a href="#lokasi" class="text-sm">Lokasi</a>
                </div>
            </div>
        </nav>

        <main class="pt-32 pb-20">
            <section class="relative max-w-6xl mx-auto px-6 text-center mb-24 overflow-hidden">
                @php
                    $sisaKamar = $sisaKamar ?? 0;
                @endphp
                <div class="hidden sm:block absolute top-0 left-0 w-40 h-40 overflow-hidden z-20 pointer-events-none">
                    @if ($sisaKamar > 0)
                        <span class="absolute top-[32px] -left-[42px] w-[190px] -rotate-45 bg-green-500 text-white text-xs font-extrabold py-2 shadow-lg flex items-center justify-center gap-1.5">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                            </span>
                            Sisa {{ $sisaKamar }} Kamar
                        </span>
                    @else
                        <span class="absolute top-[32px] -left-[42px] w-[190px] -rotate-45 bg-red-500 text-white text-xs font-extrabold py-2 shadow-lg flex items-center justify-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-white"></span>
                            Kamar Penuh
                        </span>
                    @endif
                </div>

                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-300 rounded-full text-xs font-bold mb-6">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l2 7 7 2-7 2-2 7-2-7-7-2 7-2z"></path>
                    </svg>
                    <span>Hunian chill</span>
                    <span class="w-1 h-1 bg-pink-300 rounded-full"></span>
                    <span>Sleman, Yogyakarta</span>
                </div>

                <div class="sm:hidden flex justify-center mb-6">
                    @if ($sisaKamar > 0)
                        <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-300 rounded-full text-xs font-bold">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            Sisa {{ $sisaKamar }} Kamar
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-300 rounded-full text-xs font-bold">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            Kamar Penuh
                        </span>
                    @endif
                </div>

                <h1 class="text-5xl lg:text-7xl font-extrabold mb-8 tracking-tight dark:text-white">
                    Nge-kost serasa <br><span class="gradient-text">di rumah sendiri.</span>
                </h1>
                <p class="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed mb-10">
                    Serrata Kost menyediakan hunian khusus putri dengan lingkungan yang nyaman. Gak perlu ribet, tinggal bawa koper. Cocok untuk mahasiswi dan karyawati yang mencari ketenangan di Sleman.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="https://wa.me/6285111203521" class="flex items-center justify-center gap-2 bg-[#25D366] text-white px-8 py-4 rounded-2xl font-bold shadow-lg hover:bg-[#128C7E] transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span>Chat Admin</span>
                    </a>
                </div>
            </section>

            <section id="fasilitas" class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-4 mb-24">
                <div class="md:col-span-2 md:row-span-2 relative overflow-hidden rounded-[2.5rem] shadow-sm bento-card h-[400px] md:h-full">
                    <img src="https://i.ibb.co.com/4ZkhL9Zr/Whats-App-Image-2026-03-10-at-21-57-52.jpg" 
                        class="absolute inset-0 w-full h-full object-cover z-0" alt="Kamar Utama">

                    <div class="absolute inset-0 z-20 cursor-pointer spotlight" 
                        data-media="video"
                        data-src="https://res.cloudinary.com/ddasccdw0/video/upload/v1773239074/WhatsApp_Video_2026-03-10_at_21.57.49_smlxnj.mp4"
                        data-autoplay="true"
                        data-mute="true"
                        data-title="Room Tour Serrata Kost">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent p-6 md:p-8 flex flex-col justify-end text-white">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center border border-white/30 hover:scale-110 transition-transform">
                                    <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M8 5v14l11-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl md:text-2xl font-bold">Room Tour</h3>
                            <p class="text-white/80 text-sm">Klik untuk putar video</p>
                        </div>
                    </div>

                    <div class="absolute top-6 right-6 z-30 bg-white/90 backdrop-blur px-4 py-2 rounded-full shadow-sm">
                        <span class="text-xs font-bold text-pink-600">✨ Female Only</span>
                    </div>

                    <a class="spotlight hidden" data-src="https://i.ibb.co.com/4ZkhL9Zr/Whats-App-Image-2026-03-10-at-21-57-52.jpg" data-title="Tampak Depan"></a>
                    <a class="spotlight hidden" data-src="https://i.ibb.co.com/QFGbzM3M/Whats-App-Image-2026-03-10-at-21-57-51.jpg" data-title="Teras"></a>
                    <a class="spotlight hidden" data-src="https://i.ibb.co.com/JwyyvbGV/Whats-App-Image-2026-03-10-at-21-57-49.jpg" data-title="Kamar"></a>
                    <a class="spotlight hidden" data-src="https://i.ibb.co.com/84Kc6X01/Whats-App-Image-2026-03-10-at-21-57-50.jpg" data-title="Kamar Mandi"></a>
                </div>

                <div class="bg-rose-50 dark:bg-rose-900/20 p-8 rounded-[2.5rem] border border-rose-100 dark:border-rose-800 bento-card">
                    <div class="mb-5">
                        <svg class="h-10 w-10 text-rose-500 dark:text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 18l2-9.5L11 11.5 12 5l1 6.5 4.5-3L19.5 18z"></path>
                        </svg>
                    </div>
                    <h4 class="font-bold text-lg text-rose-700 dark:text-rose-300">Khusus Putri</h4>
                    <p class="text-rose-600/70 dark:text-rose-400/80 text-sm">Lingkungan nyaman khusus mahasiswi/karyawati.</p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-700 bento-card">
                    <div class="mb-5">
                        <svg class="h-10 w-10 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                        </svg>
                    </div>
                    <h4 class="font-bold text-lg dark:text-white">WiFi Kencang</h4>
                    <p class="text-slate-400 text-sm">Nugas atau drakoran lancar jaya.</p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-700 bento-card">
                    <div class="mb-5">
                        <svg class="h-10 w-10 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4s-7 8.2-7 11a7 7 0 0014 0c0-2.8-7-11-7-11z"></path>
                        </svg>
                    </div>
                    <h4 class="font-bold text-lg dark:text-white">KM Dalam</h4>
                    <p class="text-slate-400 text-sm">Gak perlu antre, lebih privat.</p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-700 bento-card">
                    <div class="mb-5">
                        <svg class="h-10 w-10 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 002-2V2M7 2v20M21 15V2a5 5 0 00-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"></path>
                        </svg>
                    </div>
                    <h4 class="font-bold text-lg dark:text-white">Dapur Umum</h4>
                    <p class="text-slate-400 text-sm">Masak simple jadi lebih mudah.</p>
                </div>

            </section>
            
            <section id="lokasi" class="max-w-6xl mx-auto px-6">
                <div class="bg-white dark:bg-slate-800 rounded-[3rem] p-8 md:p-16 border border-slate-100 dark:border-slate-700 shadow-xl overflow-hidden relative">
                    <div class="grid lg:grid-cols-2 gap-12 relative z-10">
                        <div>
                            <h2 class="text-4xl font-extrabold mb-6 dark:text-white leading-tight">Mampir ke <br>Serrata Kost.</h2>
                            <p class="text-slate-500 dark:text-slate-400 mb-8">
                                <strong>Alamat:</strong> Jl. Pandowoharjo, Kleben Moncosan, Mancasan, Kec. Sleman, Yogyakarta 55512.
                            </p>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 20A7 7 0 019.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium dark:text-slate-300">Suasana Sejuk</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-full flex items-center justify-center">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h1a4 4 0 110 8h-1M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4V8z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 2v2M10 2v2M14 2v2"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium dark:text-slate-300">Akses ke tempat nongkrong mudah</p>
                                </div>
                            </div>
                            <div class="mt-10">
                                <a href="https://maps.app.goo.gl/ZJRMvGcMhDHTWjKD9" target="_blank" class="flex items-center justify-center gap-2 bg-[#7c3aed] text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-purple-500/20 hover:scale-105 transition">
                                    Buka Google Maps
                                </a>
                            </div>
                        </div>
                        <div class="h-[350px] rounded-[2rem] overflow-hidden border-4 border-white dark:border-slate-700 shadow-2xl">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4798.201965471629!2d110.36458057580212!3d-7.695685992321693!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5f360b5344b7%3A0xc4e4bfed6fa3cbb!2sSerrata%20Kost!5e1!3m2!1sid!2ssg!4v1768378344472!5m2!1sid!2ssg" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="py-12 text-center text-slate-400 text-sm">
            <div class="mb-4 flex justify-center gap-6">
                {{-- <a href="#" class="hover:text-pink-500 transition">Instagram</a>
                <a href="#" class="hover:text-pink-500 transition">TikTok</a> --}}
            </div>
            <p>© 2026 Serrata Kost Sleman. Chill & Comfort Living.</p>
            
            <a href="{{ route('login') }}" class="mt-4 block opacity-10 hover:opacity-100 transition-opacity duration-300 text-[10px]">
                System Access
            </a>
        </footer>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite('resources/js/welcome.js')
        @endif
    </body>
</html>