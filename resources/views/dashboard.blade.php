<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
                
                <!-- Card -->
                <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                    <div class="flex items-center space-x-4">
                        <div class="bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white"></svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">Total Kamar</p>
                            <p class="text-xl sm:text-2xl font-semibold text-gray-700">{{ $totalRooms }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card lainnya tetap sama, cukup tambahkan p-4 sm:p-6 + text-xs-->

                <!-- ... (Lakukan hal sama untuk semua card statistik) ... -->
            </div>

            <!-- Alert Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500"></svg>
                    </div>
                    <p class="ml-3 text-sm text-red-700">
                        <span class="font-medium">{{ $overduePayments }}</span> Pembayaran Terlambat
                    </p>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-500"></svg>
                    </div>
                    <p class="ml-3 text-sm text-yellow-700">
                        <span class="font-medium">{{ $pendingPayments }}</span> Pembayaran Pending
                    </p>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500"></svg>
                    </div>
                    <p class="ml-3 text-sm text-blue-700">
                        <span class="font-medium">{{ $openComplaints }}</span> Keluhan Terbuka
                    </p>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">

                <!-- Recent Payments -->
                <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Pembayaran Terbaru</h3>

                    <div class="space-y-4">
                        @forelse($recentPayments as $payment)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b pb-3">
                            <div class="mb-2 sm:mb-0">
                                <p class="font-medium text-gray-800 text-sm sm:text-base">{{ $payment->tenant->name }}</p>
                                <p class="text-xs sm:text-sm text-gray-500">Kamar {{ $payment->room->room_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800 text-sm sm:text-base">
                                    Rp {{ number_format($payment->total, 0, ',', '.') }}
                                </p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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

                <!-- Recent Complaints -->
                <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Keluhan Terbaru</h3>

                    <div class="space-y-4">
                        @forelse($recentComplaints as $complaint)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b pb-3">
                            <div class="mb-2 sm:mb-0">
                                <p class="font-medium text-gray-800 text-sm sm:text-base">{{ $complaint->title }}</p>
                                <p class="text-xs sm:text-sm text-gray-500">{{ $complaint->tenant->name }} - Kamar {{ $complaint->room->room_number }}</p>
                            </div>
                            <div>
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
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
</x-app-layout>