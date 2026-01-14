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
        
        <script src="https://cdn.jsdelivr.net/npm/spotlight.js@0.7.8/dist/spotlight.bundle.js"></script>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            body { 
                font-family: 'Plus Jakarta Sans', sans-serif; 
                scroll-behavior: smooth;
            }
            .gradient-text {
                background: linear-gradient(90deg, #db2777, #7c3aed);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .bento-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .bento-card:hover {
                transform: translateY(-5px);
            }
        </style>
    </head>
    <body class="bg-[#f8fafc] dark:bg-[#0f172a] text-[#1e293b] antialiased">

        <nav id="navbar" class="fixed top-0 inset-x-0 z-50 transition-all duration-500 ease-in-out px-4 py-4">
            <div id="navbar-bg" class="max-w-6xl mx-auto h-16 flex justify-between items-center px-8 transition-all duration-500 rounded-2xl">
                <div id="logo-text" class="text-xl font-extrabold tracking-tight text-white flex items-center gap-2 transition-colors duration-500">
                    <span>🛖 Serrata</span><span class="text-pink-500">.</span>
                </div>
                <div id="menu-text" class="flex gap-8 items-center font-bold text-white transition-colors duration-500">
                    <a href="#fasilitas" class="text-sm hover:text-pink-400 transition">Fasilitas</a>
                    <a href="#lokasi" class="text-sm hover:text-pink-400 transition">Lokasi</a>
                </div>
            </div>
        </nav>

        <main class="pt-32 pb-20">
            <section class="max-w-6xl mx-auto px-6 text-center mb-24">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-300 rounded-full text-xs font-bold mb-6">
                    <span>✨ Hunian chill</span>
                    <span class="w-1 h-1 bg-pink-300 rounded-full"></span>
                    <span>Sleman, Yogyakarta</span>
                </div>

                <h1 class="text-5xl lg:text-7xl font-extrabold mb-8 tracking-tight dark:text-white">
                    Nge-kost serasa <br><span class="gradient-text">di rumah sendiri.</span>
                </h1>
                <p class="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed mb-10">
                    Serrata Kost menyediakan hunian khusus putri dengan lingkungan yang nyaman. Gak perlu ribet, tinggal bawa koper. Cocok untuk mahasiswi dan karyawati yang mencari ketenangan di Sleman.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="https://wa.me/6285156726005" class="flex items-center justify-center gap-2 bg-[#25D366] text-white px-8 py-4 rounded-2xl font-bold shadow-lg hover:bg-[#128C7E] transition">
                        <span>💬 Admin 1</span>
                    </a>
                    <a href="https://wa.me/6285641338624" class="flex items-center justify-center gap-2 bg-[#25D366] text-white px-8 py-4 rounded-2xl font-bold shadow-lg hover:bg-[#128C7E] transition">
                        <span>💬 Admin 2</span>
                    </a>
                </div>
            </section>

            <section id="fasilitas" class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-4 mb-24">
                
                <div class="md:col-span-2 md:row-span-2 relative overflow-hidden rounded-[2.5rem] shadow-sm bento-card spotlight" 
                        data-src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=1000"
                        data-title="Kamar Tipe A"
                        data-description="Fasilitas kasur empuk dan lemari besar">
                    
                    <div class="absolute top-6 right-6 z-10 bg-white/90 backdrop-blur px-4 py-2 rounded-full shadow-sm">
                        <span class="text-xs font-bold text-pink-600">✨ Female Only</span>
                    </div>

                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent p-8 flex flex-col justify-end text-white">
                        <h3 class="text-2xl font-bold">Kamar Minimalis</h3>
                        <p class="text-white/80">Klik untuk lihat detail kamar ✨</p>
                    </div>
                    
                    <a class="spotlight hidden" data-src="https://images.unsplash.com/photo-1560185127-6ed189bf02f4?q=80&w=1000"></a>
                    <a class="spotlight hidden" data-src="https://images.unsplash.com/photo-1598928506311-c55ded91a20c?q=80&w=1000"></a>
                    <a class="spotlight hidden" data-src="https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=1000"></a>
                </div>

                <div class="bg-rose-50 dark:bg-rose-900/20 p-8 rounded-[2.5rem] border border-rose-100 dark:border-rose-800 bento-card">
                    <div class="text-4xl mb-4">👸</div>
                    <h4 class="font-bold text-lg text-rose-700 dark:text-rose-300">Khusus Putri</h4>
                    <p class="text-rose-600/70 dark:text-rose-400/80 text-sm">Lingkungan nyaman khusus mahasiswi/karyawati.</p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-700 bento-card">
                    <div class="text-3xl mb-4">📶</div>
                    <h4 class="font-bold text-lg dark:text-white">WiFi Kencang</h4>
                    <p class="text-slate-400 text-sm">Nugas atau drakoran lancar jaya.</p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-700 bento-card">
                    <div class="text-3xl mb-4">🛁</div>
                    <h4 class="font-bold text-lg dark:text-white">KM Dalam</h4>
                    <p class="text-slate-400 text-sm">Gak perlu antre, lebih privat.</p>
                </div>

                <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-700 bento-card">
                    <div class="text-3xl mb-4">🍲</div>
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
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 text-green-600 rounded-full flex items-center justify-center">🌴</div>
                                    <p class="text-sm font-medium dark:text-slate-300">Suasana Sejuk</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 text-orange-600 rounded-full flex items-center justify-center">🍕</div>
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
                <a href="#" class="hover:text-pink-500 transition">Instagram</a>
                <a href="#" class="hover:text-pink-500 transition">TikTok</a>
            </div>
            <p>© 2026 Serrata Kost Sleman. Chill & Comfort Living.</p>
            
            <a href="{{ route('login') }}" class="mt-4 block opacity-10 hover:opacity-100 transition-opacity duration-300 text-[10px]">
                System Access
            </a>
        </footer>

        <script>
            window.onscroll = function() {
                const navbar = document.getElementById('navbar');
                const bg = document.getElementById('navbar-bg');
                const logo = document.getElementById('logo-text');
                const menu = document.getElementById('menu-text');
                
                if (window.scrollY > 50) {
                    // SAAT SCROLL (Jadi Floating Card yang Modern)
                    navbar.classList.add('pt-6'); // Memberi jarak dari atas layar
                    bg.classList.add('bg-white/80', 'backdrop-blur-xl', 'shadow-lg', 'border', 'border-white/20');
                    bg.classList.remove('max-w-6xl');
                    bg.classList.add('max-w-4xl'); // Mengecilkan lebar navbar agar terlihat "floating"
                    
                    // Ubah warna teks jadi gelap karena background navbar jadi putih transparan
                    logo.classList.replace('text-white', 'text-slate-900');
                    menu.classList.replace('text-white', 'text-slate-900');
                } else {
                    // SAAT DI ATAS (Menyatu dengan Background)
                    navbar.classList.remove('pt-6');
                    bg.classList.remove('bg-white/80', 'backdrop-blur-xl', 'shadow-lg', 'border', 'border-white/20', 'max-w-4xl');
                    bg.classList.add('max-w-6xl');
                    
                    // Kembalikan teks jadi putih agar terlihat di background gelap
                    logo.classList.replace('text-slate-900', 'text-white');
                    menu.classList.replace('text-slate-900', 'text-white');
                }
            };
        </script>
    </body>
</html>