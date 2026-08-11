<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Laporan Keuangan') }}
        </h2>
    </x-slot>

    <div class="page-container pt-4 sm:pt-5 pb-8 sm:pb-10">

        @php
            $monthOptions = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthOptions[$m] = \Carbon\Carbon::createFromDate(null, $m, 1)->format('F');
            }
            $yearOptions = ['' => 'Semua'];
            for ($y = now()->year + 1; $y >= 2023; $y--) { $yearOptions[$y] = $y; }
        @endphp

        <div class="no-print">
            <form method="GET">
                <x-filter-panel reset="{{ route('finances.report') }}">
                    <x-filter-select name="month" label="Bulan" :options="$monthOptions" />
                    <x-filter-select name="year" label="Tahun" :options="$yearOptions" />
                    <x-slot:extra>
                        <a href="{{ route('finances.report', array_merge(request()->all(), ['download' => 'pdf'])) }}"
                            class="btn-danger btn-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Export PDF
                        </a>
                    </x-slot:extra>
                </x-filter-panel>
            </form>
        </div>

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="card p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-stone-500">Total Pemasukan</p>
                        <p class="mt-1 text-2xl font-extrabold text-emerald-600 tabular">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                </div>
            </div>
            <div class="card p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-stone-500">Total Pengeluaran</p>
                        <p class="mt-1 text-2xl font-extrabold text-red-600 tabular">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    </div>
                </div>
            </div>
            <div class="card p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-stone-500">Saldo Bulan Ini</p>
                        <p class="mt-1 text-2xl font-extrabold tabular {{ $balance >= 0 ? 'text-brand-600' : 'text-orange-600' }}">Rp {{ number_format($balance, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl {{ $balance >= 0 ? 'bg-brand-100 text-brand-600' : 'bg-orange-100 text-orange-600' }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="card overflow-hidden">
                <div class="border-b border-stone-100 px-5 py-4">
                    <h3 class="section-title">Pemasukan per Kategori</h3>
                </div>
                <div class="p-5">
                    @if($incomeByCategory->count() > 0)
                    <div class="space-y-4">
                        @foreach($incomeByCategory as $item)
                        <div>
                            <div class="mb-1.5 flex items-center justify-between">
                                <p class="text-sm font-medium text-stone-900">{{ $item['category'] }}</p>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-emerald-600 tabular">Rp {{ number_format($item['total'], 0, ',', '.') }}</p>
                                    <p class="text-xs text-stone-500">{{ $totalIncome > 0 ? number_format($item['total'] / $totalIncome * 100, 1) : 0 }}%</p>
                                </div>
                            </div>
                            <div class="h-2 rounded-full bg-stone-100">
                                <div class="h-2 rounded-full bg-emerald-500"
                                     style="width: {{ $totalIncome > 0 ? ($item['total'] / $totalIncome * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="py-8 text-center text-sm text-stone-500">Belum ada pemasukan bulan ini</p>
                    @endif
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="border-b border-stone-100 px-5 py-4">
                    <h3 class="section-title">Pengeluaran per Kategori</h3>
                </div>
                <div class="p-5">
                    @if($expenseByCategory->count() > 0)
                    <div class="space-y-4">
                        @foreach($expenseByCategory as $item)
                        <div>
                            <div class="mb-1.5 flex items-center justify-between">
                                <p class="text-sm font-medium text-stone-900">{{ $item['category'] }}</p>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-red-600 tabular">Rp {{ number_format($item['total'], 0, ',', '.') }}</p>
                                    <p class="text-xs text-stone-500">{{ $totalExpense > 0 ? number_format($item['total'] / $totalExpense * 100, 1) : 0 }}%</p>
                                </div>
                            </div>
                            <div class="h-2 rounded-full bg-stone-100">
                                <div class="h-2 rounded-full bg-red-500"
                                     style="width: {{ $totalExpense > 0 ? ($item['total'] / $totalExpense * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="py-8 text-center text-sm text-stone-500">Belum ada pengeluaran bulan ini</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="border-b border-stone-100 px-5 py-4">
                <h3 class="section-title">Tren 12 Bulan Terakhir</h3>
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
                                Rp {{ number_format($trend['income'], 0, ',', '.') }}
                            </td>
                            <td class="text-right font-semibold text-red-600 tabular">
                                Rp {{ number_format($trend['expense'], 0, ',', '.') }}
                            </td>
                            @php
                                $trendBalance = $trend['balance']; // Sudah dihitung di controller
                            @endphp
                            <td class="text-right font-bold tabular {{ $trendBalance >= 0 ? 'text-brand-600' : 'text-orange-600' }}">
                                Rp {{ number_format($trendBalance, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="border-b border-stone-100 px-5 py-4">
                <h3 class="section-title">
                    Detail Transaksi -
                    {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-max w-full">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-right">Pemasukan</th>
                            <th class="text-right">Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($financesPage as $finance)
                        <tr>
                            <td>
                                {{ $finance->transaction_date->format('d M Y') }}
                            </td>
                            <td>
                                <span class="font-medium text-stone-900">{{ $finance->category }}</span>
                            </td>
                            <td class="max-w-xs whitespace-normal">
                                {{ $finance->description }}
                            </td>
                            <td class="text-right font-semibold text-emerald-600 tabular">
                                {{ $finance->type == 'income' ? 'Rp ' . number_format($finance->amount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right font-semibold text-red-600 tabular">
                                {{ $finance->type == 'expense' ? 'Rp ' . number_format($finance->amount, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10">
                                <div class="empty-state">
                                    <svg class="h-10 w-10 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="mt-3 text-sm font-semibold text-stone-500">Tidak ada transaksi pada periode ini</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-bold">
                            <td colspan="3" class="text-right text-stone-900">TOTAL:</td>
                            <td class="text-right text-lg font-extrabold text-emerald-600 tabular">
                                Rp {{ number_format($totalIncome, 0, ',', '.') }}
                            </td>
                            <td class="text-right text-lg font-extrabold text-red-600 tabular">
                                Rp {{ number_format($totalExpense, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="font-bold">
                            <td colspan="3" class="text-right text-stone-900">SALDO:</td>
                            <td colspan="2" class="text-right text-lg font-extrabold tabular {{ $balance >= 0 ? 'text-brand-600' : 'text-orange-600' }}">
                                Rp {{ number_format($balance, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <x-pagination :paginator="$financesPage" />
        </div>

    </div>
</x-app-layout>
