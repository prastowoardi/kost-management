    <x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="page-title whitespace-nowrap">
                {{ __('Dashboard Keuangan') }}
            </h2>
            <div class="flex flex-wrap justify-end gap-2">
                <a href="{{ route('finances.report') }}" class="btn-success btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan
                </a>
                <a href="{{ route('finances.index') }}" class="btn-secondary btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Pencatatan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="page-container space-y-6 pt-4 sm:pt-5 pb-8 sm:pb-10">

        <!-- Current Month Stats -->
        <div>
            <h3 class="section-title mb-4">Bulan Ini ({{ now()->format('F Y') }})</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="card p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-stone-500">Pemasukan</p>
                            <p class="mt-1 text-2xl font-extrabold text-emerald-600 tabular">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-stone-500">Pengeluaran</p>
                            <p class="mt-1 text-2xl font-extrabold text-red-600 tabular">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-stone-500">Saldo Bulan Ini</p>
                            <p class="mt-1 text-2xl font-extrabold tabular {{ $monthlyBalance >= 0 ? 'text-brand-600' : 'text-orange-600' }}">
                                Rp {{ number_format($monthlyBalance, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl {{ $monthlyBalance >= 0 ? 'bg-brand-100 text-brand-600' : 'bg-orange-100 text-orange-600' }}">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Year to Date Stats -->
        <div>
            <h3 class="section-title mb-4">Tahun Ini ({{ now()->year }})</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="card p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-stone-500">Total Pemasukan</p>
                            <p class="mt-1 text-2xl font-extrabold text-emerald-600 tabular">Rp {{ number_format($ytdIncome, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-stone-500">Total Pengeluaran</p>
                            <p class="mt-1 text-2xl font-extrabold text-red-600 tabular">Rp {{ number_format($ytdExpense, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-stone-500">Saldo Tahun Ini</p>
                            <p class="mt-1 text-2xl font-extrabold tabular {{ $ytdBalance >= 0 ? 'text-brand-600' : 'text-orange-600' }}">
                                Rp {{ number_format($ytdBalance, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl {{ $ytdBalance >= 0 ? 'bg-brand-100 text-brand-600' : 'bg-orange-100 text-orange-600' }}">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart & Recent Transactions -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            <!-- Anually Trend Chart -->
            <div class="card overflow-hidden">
                <div class="border-b border-stone-100 px-5 py-4">
                    <h3 class="section-title">Ringkasan Setahun Terakhir</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-max w-full">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th class="text-right">Pemasukan</th>
                                <th class="text-right">Pengeluaran</th>
                                <th class="text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyTrend as $trend)
                            <tr>
                                <td class="font-medium text-stone-900">{{ $trend['month'] }}</td>
                                <td class="text-right font-semibold text-emerald-600 tabular">
                                    {{ number_format($trend['income'], 0, ',', '.') }}
                                </td>
                                <td class="text-right font-semibold text-red-600 tabular">
                                    {{ number_format($trend['expense'], 0, ',', '.') }}
                                </td>
                                <td class="text-right font-bold tabular {{ $trend['balance'] >= 0 ? 'text-brand-600' : 'text-orange-600' }}">
                                    {{ number_format($trend['balance'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card overflow-hidden">
                <div class="border-b border-stone-100 px-5 py-4">
                    <h3 class="section-title">Transaksi Terbaru</h3>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        @forelse($recentTransactions as $transaction)
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full {{ $transaction->type == 'income' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $transaction->type == 'income' ? 'M7 11l5-5m0 0l5 5m-5-5v12' : 'M17 13l-5 5m0 0l-5-5m5 5V6' }}"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-stone-900">{{ $transaction->description }}</p>
                                    <p class="text-xs text-stone-500">{{ $transaction->category }} • {{ $transaction->transaction_date->format('d M Y') }}</p>
                                </div>
                            </div>
                            <p class="flex-shrink-0 text-sm font-semibold tabular {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $transaction->type == 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </p>
                        </div>
                        @empty
                        <p class="py-8 text-center text-sm text-stone-500">Belum ada transaksi</p>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('finances.index') }}" class="text-sm font-medium text-brand-600 transition hover:text-brand-700">
                            Lihat Semua Transaksi →
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
