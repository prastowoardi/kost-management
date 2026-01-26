<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Serrata Kost</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animated-gradient {
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #4facfe);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        .pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="animated-gradient min-h-screen flex items-center justify-center p-4">
    
    <!-- Decorative Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <div class="w-full max-w-6xl mx-auto grid lg:grid-cols-2 gap-8 items-center relative z-10">
        
        <!-- Left Side - Illustration/Info -->
        <div class="hidden lg:block text-white space-y-8">
            <div class="floating">
                <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-8 border border-white/20">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-4xl">
                            🏠
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold">Serrata Kost</h1>
                            <p class="text-white/80">Sistem Manajemen Kos Modern</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                ✓
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Kelola Kamar & Penghuni</h3>
                                <p class="text-white/70 text-sm">Manajemen kamar, penghuni, dan fasilitas dalam satu platform</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                ✓
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Pembayaran & Keuangan</h3>
                                <p class="text-white/70 text-sm">Catat pembayaran sewa dan kelola keuangan dengan mudah</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                ✓
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Laporan Real-time</h3>
                                <p class="text-white/70 text-sm">Dashboard dan laporan lengkap untuk monitoring bisnis kos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center space-y-2">
                <div class="flex justify-center space-x-2">
                    <div class="w-2 h-2 bg-white/60 rounded-full pulse-slow"></div>
                    <div class="w-2 h-2 bg-white/60 rounded-full pulse-slow" style="animation-delay: 0.2s;"></div>
                    <div class="w-2 h-2 bg-white/60 rounded-full pulse-slow" style="animation-delay: 0.4s;"></div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="glass-effect rounded-3xl shadow-2xl p-8 md:p-12 border border-white/20">
            
            <!-- Mobile Logo -->
            <div class="lg:hidden text-center mb-8">
                <div class="inline-flex items-center space-x-3 text-gray-800">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center text-2xl">
                        🏠
                    </div>
                    <div class="text-left">
                        <h1 class="text-2xl font-bold">Serrata Kostt</h1>
                        <p class="text-sm text-gray-600">Sistem Manajemen Kos</p>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back! 👋</h2>
                <p class="text-gray-600">Silakan login untuk melanjutkan</p>
            </div>

            <!-- Session Status -->
            @if(session('status'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center space-x-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            <!-- Success Message -->
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center space-x-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center space-x-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="block w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                    </div>
                    @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" 
                                type="password" 
                                name="password" 
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="block w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                    </div>
                    @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                {{-- <div class="flex items-center justify-between">
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-purple-600 hover:text-purple-700 transition">
                        Forgot Password?
                    </a>
                    @endif
                </div> --}}

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold py-3.5 px-4 rounded-xl transition duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg">
                    Sign In
                </button>
            </form>

            <!-- Demo Accounts -->
            {{-- <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-center text-sm text-gray-600 mb-4">Demo Accounts:</p>
                <div class="grid grid-cols-3 gap-3 text-xs">
                    <div class="bg-purple-50 p-3 rounded-lg border border-purple-100">
                        <p class="font-semibold text-purple-700 mb-1">Admin</p>
                        <p class="text-gray-600">admin@kos.com</p>
                        <p class="text-gray-500">password</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                        <p class="font-semibold text-green-700 mb-1">Staff</p>
                        <p class="text-gray-600">staff@kos.com</p>
                        <p class="text-gray-500">password</p>
                    </div>
                    <div class="bg-orange-50 p-3 rounded-lg border border-orange-100">
                        <p class="font-semibold text-orange-700 mb-1">Tenant</p>
                        <p class="text-gray-600">tenant@kos.com</p>
                        <p class="text-gray-500">password</p>
                    </div>
                </div>
            </div> --}}

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    © 2025 Serrata Kostt. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        
        .animate-blob {
            animation: blob 7s infinite;
        }
        
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</body>
</html>