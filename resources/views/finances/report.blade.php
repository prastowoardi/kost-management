<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Keuangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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
                                class="px-4 py-2 text-xs font-bold rounded-lg bg-red-500 text-white hover:bg-red-600 transition flex items-center gap-1.5 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Export PDF
                            </a>
                        </x-slot:extra>
                    </x-filter-panel>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-gradient-to-br from-green-500 to-green-600 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <h3 class="text-sm opacity-90 mb-2">Total Pemasukan</h3>
                        <p class="text-3xl font-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-red-600 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <h3 class="text-sm opacity-90 mb-2">Total Pengeluaran</h3>
                        <p class="text-3xl font-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="bg-gradient-to-br {{ $balance >= 0 ? 'from-blue-500 to-blue-600' : 'from-orange-500 to-orange-600' }} overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <h3 class="text-sm opacity-90 mb-2">Saldo Bulan Ini</h3>
                        <p class="text-3xl font-bold">Rp {{ number_format($balance, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pemasukan per Kategori</h3>
                        @if($incomeByCategory->count() > 0)
                        <div class="space-y-3">
                            @foreach($incomeByCategory as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $item['category'] }}</p>
                                    <div class="mt-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" 
                                             style="width: {{ $totalIncome > 0 ? ($item['total'] / $totalIncome * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="ml-4 text-right">
                                    <p class="text-sm font-semibold text-green-600">Rp {{ number_format($item['total'], 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500">{{ $totalIncome > 0 ? number_format($item['total'] / $totalIncome * 100, 1) : 0 }}%</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 text-center py-8">Belum ada pemasukan bulan ini</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengeluaran per Kategori</h3>
                        @if($expenseByCategory->count() > 0)
                        <div class="space-y-3">
                            @foreach($expenseByCategory as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $item['category'] }}</p>
                                    <div class="mt-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-red-500 h-2 rounded-full" 
                                             style="width: {{ $totalExpense > 0 ? ($item['total'] / $totalExpense * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="ml-4 text-right">
                                    <p class="text-sm font-semibold text-red-600">Rp {{ number_format($item['total'], 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500">{{ $totalExpense > 0 ? number_format($item['total'] / $totalExpense * 100, 1) : 0 }}%</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 text-center py-8">Belum ada pengeluaran bulan ini</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren 12 Bulan Terakhir</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="border-b">
                                <tr>
                                    <th class="text-left py-2 px-4 text-sm font-medium text-gray-600">Bulan</th>
                                    <th class="text-right py-2 px-4 text-sm font-medium text-gray-600">Pemasukan</th>
                                    <th class="text-right py-2 px-4 text-sm font-medium text-gray-600">Pengeluaran</th>
                                    <th class="text-right py-2 px-4 text-sm font-medium text-gray-600">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyTrend as $trend)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $trend['month'] }}</td>
                                    <td class="py-3 px-4 text-sm text-right text-green-600 font-semibold">
                                        Rp {{ number_format($trend['income'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-right text-red-600 font-semibold">
                                        Rp {{ number_format($trend['expense'], 0, ',', '.') }}
                                    </td>
                                    @php
                                        $trendBalance = $trend['balance']; // Sudah dihitung di controller
                                    @endphp
                                    <td class="py-3 px-4 text-sm text-right font-bold {{ $trendBalance >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                                        Rp {{ number_format($trendBalance, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Detail Transaksi - 
                        {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pemasukan</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pengeluaran</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($finances as $finance)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $finance->transaction_date->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $finance->category }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $finance->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-600">
                                        {{ $finance->type == 'income' ? 'Rp ' . number_format($finance->amount, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-red-600">
                                        {{ $finance->type == 'expense' ? 'Rp ' . number_format($finance->amount, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Tidak ada transaksi pada periode ini
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr class="font-bold">
                                    <td colspan="3" class="px-6 py-4 text-right text-gray-900">TOTAL:</td>
                                    <td class="px-6 py-4 text-right text-green-600 text-lg">
                                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-red-600 text-lg">
                                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="font-bold">
                                    <td colspan="3" class="px-6 py-4 text-right text-gray-900">SALDO:</td>
                                    <td colspan="2" class="px-6 py-4 text-right {{ $balance >= 0 ? 'text-blue-600' : 'text-orange-600' }} text-xl">
                                        Rp {{ number_format($balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</x-app-layout>