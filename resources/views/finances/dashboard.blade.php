    <x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Keuangan') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('finances.report') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    📄 Laporan
                </a>
                <a href="{{ route('finances.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    ← Semua Transaksi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Current Month Stats -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Bulan Ini ({{ now()->format('F Y') }})</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-gray-600 font-medium">Pemasukan</h4>
                                <div class="bg-green-100 p-2 rounded-lg">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-green-600">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-gray-600 font-medium">Pengeluaran</h4>
                                <div class="bg-red-100 p-2 rounded-lg">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-red-600">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-gray-600 font-medium">Saldo Bulan Ini</h4>
                                <div class="{{ $monthlyBalance >= 0 ? 'bg-blue-100' : 'bg-orange-100' }} p-2 rounded-lg">
                                    <svg class="h-6 w-6 {{ $monthlyBalance >= 0 ? 'text-blue-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold {{ $monthlyBalance >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                                Rp {{ number_format($monthlyBalance, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Year to Date Stats -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tahun Ini ({{ now()->year }})</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <p class="text-sm opacity-90">Total Pemasukan</p>
                            <p class="text-2xl font-bold mt-2">Rp {{ number_format($ytdIncome, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-red-500 to-red-600 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <p class="text-sm opacity-90">Total Pengeluaran</p>
                            <p class="text-2xl font-bold mt-2">Rp {{ number_format($ytdExpense, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br {{ $ytdBalance >= 0 ? 'from-blue-500 to-blue-600' : 'from-orange-500 to-orange-600' }} overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <p class="text-sm opacity-90">Saldo Tahun Ini</p>
                            <p class="text-2xl font-bold mt-2">Rp {{ number_format($ytdBalance, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart & Recent Transactions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Monthly Trend Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren 6 Bulan Terakhir</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2 text-sm font-medium text-gray-600">Bulan</th>
                                        <th class="text-right py-2 text-sm font-medium text-gray-600">Pemasukan</th>
                                        <th class="text-right py-2 text-sm font-medium text-gray-600">Pengeluaran</th>
                                        <th class="text-right py-2 text-sm font-medium text-gray-600">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyTrend as $trend)
                                    <tr class="border-b">
                                        <td class="py-3 text-sm text-gray-900">{{ $trend['month'] }}</td>
                                        <td class="py-3 text-sm text-right text-green-600 font-semibold">
                                            {{ number_format($trend['income'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 text-sm text-right text-red-600 font-semibold">
                                            {{ number_format($trend['expense'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 text-sm text-right font-semibold {{ $trend['balance'] >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                                            {{ number_format($trend['balance'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Transaksi Terbaru</h3>
                        <div class="space-y-3">
                            @forelse($recentTransactions as $transaction)
                            <div class="flex items-center justify-between border-b pb-3">
                                <div class="flex items-start space-x-3">
                                    <div class="mt-1">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center 
                                            {{ $transaction->type == 'income' ? 'bg-green-100' : 'bg-red-100' }}">
                                            <span class="text-lg">{{ $transaction->type == 'income' ? '💰' : '💸' }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $transaction->description }}</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->category }} • {{ $transaction->transaction_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-8">Belum ada transaksi</p>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('finances.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Semua Transaksi →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>