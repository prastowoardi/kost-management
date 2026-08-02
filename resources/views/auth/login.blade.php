<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Serrata Kost</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-b from-white via-brand-50/50 to-stone-100 px-4 py-10">
        <!-- Ambient blobs -->
        <div class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-brand-100/70 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 -right-32 h-[28rem] w-[28rem] rounded-full bg-brand-200/40 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/2 top-1/3 h-64 w-64 -translate-x-1/2 rounded-full bg-purple-100/50 blur-3xl"></div>

        <div class="relative z-10 grid w-full max-w-5xl items-center gap-10 lg:grid-cols-2">
            <!-- Left Side - Illustration/Info -->
            <div class="hidden lg:block">
                <a href="/" class="mb-10 flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 shadow-soft">
                        <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    </span>
                    <span class="text-2xl font-extrabold tracking-tight text-stone-900">
                        Serrata <span class="text-brand-600">Kost</span>
                    </span>
                </a>

                <h1 class="text-4xl font-extrabold leading-tight tracking-tight text-stone-900">
                    Kelola kost kamu,<br>
                    <span class="text-brand-600">lebih tenang & simpel.</span>
                </h1>
                <p class="mt-4 max-w-md text-stone-500">
                    Sistem manajemen kos untuk kamar, penghuni, pembayaran, dan keuangan dalam satu tempat.
                </p>

                <div class="mt-10 space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-lg shadow-soft ring-1 ring-stone-100">🏠</div>
                        <div>
                            <h3 class="font-bold text-stone-900">Kelola Kamar & Penghuni</h3>
                            <p class="text-sm text-stone-500">Manajemen kamar, penghuni, dan fasilitas dalam satu platform.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-lg shadow-soft ring-1 ring-stone-100">💳</div>
                        <div>
                            <h3 class="font-bold text-stone-900">Pembayaran & Keuangan</h3>
                            <p class="text-sm text-stone-500">Catat pembayaran sewa dan kelola keuangan dengan mudah.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-lg shadow-soft ring-1 ring-stone-100">📊</div>
                        <div>
                            <h3 class="font-bold text-stone-900">Laporan Real-time</h3>
                            <p class="text-sm text-stone-500">Dashboard dan laporan lengkap untuk monitoring bisnis kos.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="mx-auto w-full max-w-md">
                <div class="rounded-3xl border border-stone-100 bg-white p-7 shadow-card sm:p-9">
                    <!-- Mobile Logo -->
                    <div class="mb-7 lg:hidden">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 shadow-soft">
                                <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-extrabold tracking-tight text-stone-900">Serrata Kost</h1>
                                <p class="text-sm text-stone-500">Sistem Manajemen Kos</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-7">
                        <h2 class="text-2xl font-extrabold tracking-tight text-stone-900">Welcome back</h2>
                        <p class="mt-1 text-stone-500">Silakan login untuk melanjutkan</p>
                    </div>

                    <!-- Session Status -->
                    @if(session('status'))
                    <div class="alert-success mb-4">
                        <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                    @endif

                    <!-- Success Message -->
                    @if(session('success'))
                    <div class="alert-success mb-4">
                        <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    <!-- Error Message -->
                    @if(session('error'))
                    <div class="alert-error mb-4">
                        <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <!-- Email -->
                        <div>
                            <label for="email" class="form-label mb-1.5">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <svg class="h-5 w-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input id="email" 
                                        type="email" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required 
                                        autofocus
                                        autocomplete="username"
                                        placeholder="nama@email.com"
                                        class="block w-full rounded-xl border-stone-200 bg-white py-3 pl-12 pr-4 text-sm text-stone-900 shadow-sm outline-none transition placeholder:text-stone-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500">
                            </div>
                            @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="form-label mb-1.5">
                                Password
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <svg class="h-5 w-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input id="password" 
                                        type="password" 
                                        name="password" 
                                        required
                                        autocomplete="current-password"
                                        placeholder="••••••••"
                                        class="block w-full rounded-xl border-stone-200 bg-white py-3 pl-12 pr-4 text-sm text-stone-900 shadow-sm outline-none transition placeholder:text-stone-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500">
                            </div>
                            @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remember Me & Forgot Password
                        <div class="flex items-center justify-between">
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700 transition">
                                Forgot Password?
                            </a>
                            @endif
                        </div>
                        --}}

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="btn btn-primary w-full">
                            Sign In
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="mt-7 border-t border-stone-100 pt-5 text-center">
                        <p class="text-sm text-stone-400">
                            © 2026 Serrata Kost. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
