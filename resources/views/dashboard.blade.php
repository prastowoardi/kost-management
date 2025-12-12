<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12"> {{-- Mengurangi padding atas/bawah di mobile untuk tampilan lebih ringkas --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> {{-- Menambahkan padding horizontal (px-4) untuk mobile --}}
            
            <h3 class="text-xl font-bold text-gray-800 mb-4">Ringkasan Statistik</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6"> {{-- Mengubah md:grid-cols-2 menjadi sm:grid-cols-2 agar 2 kolom muncul lebih cepat di tablet kecil/mode landscape --}}
                
                {{-- REVISI KARTU STATISTIK (memastikan konten tetap sejajar di mobile) --}}
                
                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg transition duration-300 hover:shadow-lg">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-lg p-3"> {{-- Menggunakan rounded-lg --}}
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <div class="ml-4 sm:ml-5">
                                <p class="text-gray-500 text-sm truncate">Total Kamar</p> {{-- Menambah truncate --}}
                                <p class="text-xl sm:text-2xl font-bold text-gray-700">{{ $totalRooms }}</p> {{-- Mengubah font-size untuk mobile --}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg transition duration-300 hover:shadow-lg">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-lg p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4 sm:ml-5">
                                <p class="text-gray-500 text-sm truncate">Kamar Terisi</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-700">{{ $occupiedRooms }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg transition duration-300 hover:shadow-lg">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-lg p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4 sm:ml-5">
                                <p class="text-gray-500 text-sm truncate">Penghuni Aktif</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-700">{{ $activeTenants }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg transition duration-300 hover:shadow-lg">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-lg p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4 sm:ml-5">
                                <p class="text-gray-500 text-sm truncate">Pendapatan Bulan Ini</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-700">Rp {{ number_format($paymentsThisMonth, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-4">Peringatan Cepat</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6"> {{-- Mengurangi gap di mobile --}}
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm"> {{-- Mengubah rounded --}}
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                <span class="font-bold text-base">{{ $overduePayments }}</span> Pembayaran Terlambat
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <span class="font-bold text-base">{{ $pendingPayments }}</span> Pembayaran Pending
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <span class="font-bold text-base">{{ $openComplaints }}</span> Keluhan Terbuka
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Terbaru</h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Pembayaran Terbaru</h3>
                        <div class="space-y-4"> {{-- Meningkatkan space-y --}}
                            @forelse($recentPayments as $payment)
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b pb-3"> {{-- Fleksibel di mobile --}}
                                <div class="mb-1 sm:mb-0">
                                    <p class="font-medium text-gray-800">{{ $payment->tenant->name }}</p>
                                    <p class="text-sm text-gray-500">Kamar {{ $payment->room->room_number }}</p>
                                </div>
                                <div class="text-left sm:text-right flex items-center space-x-3">
                                    <p class="font-semibold text-gray-800 text-sm sm:text-base">Rp {{ number_format($payment->total, 0, ',', '.') }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        @if($payment->status == 'paid') bg-green-100 text-green-800
                                        @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4">Belum ada pembayaran</p>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('payments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Semua →
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Keluhan Terbaru</h3>
                        <div class="space-y-4"> {{-- Meningkatkan space-y --}}
                            @forelse($recentComplaints as $complaint)
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b pb-3"> {{-- Fleksibel di mobile --}}
                                <div class="mb-1 sm:mb-0">
                                    <p class="font-medium text-gray-800 truncate max-w-xs">{{ $complaint->title }}</p> {{-- Menambah truncate --}}
                                    <p class="text-sm text-gray-500">{{ $complaint->tenant->name }} - Kamar {{ $complaint->room->room_number }}</p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        @if($complaint->status == 'resolved') bg-green-100 text-green-800
                                        @elseif($complaint->status == 'in_progress') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($complaint->status) }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4">Belum ada keluhan</p>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('complaints.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Semua →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>