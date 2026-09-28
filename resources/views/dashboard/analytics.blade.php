@php
    use App\Support\Chart;

    $revenue = $analytics['revenue'];
    $arrears = $analytics['arrears'];
    $occupancy = $analytics['occupancy'];

    $periodOptions = [3 => '3 Bulan', 6 => '6 Bulan', 12 => '12 Bulan'];

    $totalIncome = collect($revenue)->sum('income');
    $totalExpense = collect($revenue)->sum('expense');
    $avgIncome = count($revenue) > 0 ? intdiv($totalIncome, count($revenue)) : 0;

    $agingColors = [
        'not_due' => '#a8a29e',
        'd1_7' => '#f59e0b',
        'd8_15' => '#f97316',
        'd16_30' => '#ef4444',
        'd30_plus' => '#991b1b',
    ];

    $agingBars = collect($arrears['buckets'])
        ->map(
            fn($bucket) => [
                'label' => $bucket['label'],
                'value' => $bucket['amount'],
                'count' => $bucket['count'],
                'color' => $agingColors[$bucket['key']] ?? '#a8a29e',
                'meta' => $bucket['count'] === 0 ? 'Tidak ada' : $bucket['count'] . ' penghuni',
            ],
        )
        ->all();

    $roomSlices = [
        ['label' => 'Terisi', 'value' => $occupancy['occupied_rooms'], 'color' => '#0d9488'],
        ['label' => 'Tersedia', 'value' => $occupancy['available_rooms'], 'color' => '#2dd4bf'],
        ['label' => 'Perbaikan', 'value' => $occupancy['maintenance_rooms'], 'color' => '#f59e0b'],
    ];

    $revenueSeries = [
        [
            'key' => 'income',
            'label' => 'Pemasukan',
            'color' => '#0d9488',
            'values' => array_column($revenue, 'income'),
        ],
        [
            'key' => 'expense',
            'label' => 'Pengeluaran',
            'color' => '#ef4444',
            'values' => array_column($revenue, 'expense'),
        ],
    ];

    $occupancySeries = [
        [
            'key' => 'rate',
            'label' => 'Tingkat Okupansi',
            'color' => '#0d9488',
            'values' => array_column($occupancy['points'], 'rate'),
        ],
    ];

    $labels = array_column($revenue, 'label');
    $worstBucket = collect($arrears['buckets'])->last();
@endphp

<div class="mb-10">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h3 class="section-title flex items-center">
            <span class="mr-3 rounded-lg bg-brand-500 p-1.5 shadow-md shadow-brand-100">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </span>
            Analitik
        </h3>

        <div class="inline-flex rounded-xl bg-stone-200/70 p-1" role="tablist" aria-label="Periode analitik">
            @foreach ($periodOptions as $value => $caption)
                <a href="{{ route('dashboard', ['months' => $value]) }}"
                    class="rounded-lg px-3 py-1.5 text-xs font-bold transition
                          {{ $analytics['months'] === $value ? 'bg-white text-brand-700 shadow-sm' : 'text-stone-500 hover:text-stone-700' }}">
                    {{ $caption }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- TREN PENDAPATAN --}}
        <div class="card overflow-hidden lg:col-span-3">
            <div class="card-header bg-stone-50/60">
                <div>
                    <h4 class="section-title">Tren Pendapatan</h4>
                    <p class="mt-0.5 text-xs text-stone-400">Pemasukan vs pengeluaran {{ $analytics['months'] }} bulan
                        terakhir</p>
                </div>
                <a href="{{ route('finances.report') }}" class="btn-secondary btn-sm">
                    Laporan
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="card-body">
                <div class="mb-5 grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-xs font-medium text-stone-500">Total Pemasukan</p>
                        <p class="text-lg font-extrabold tabular text-emerald-600">
                            {{ Chart::compactNumber($totalIncome) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-stone-500">Total Pengeluaran</p>
                        <p class="text-lg font-extrabold tabular text-red-600">{{ Chart::compactNumber($totalExpense) }}
                        </p>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-xs font-medium text-stone-500">Rata-rata / Bulan</p>
                        <p class="text-lg font-extrabold tabular text-brand-700">{{ Chart::compactNumber($avgIncome) }}
                        </p>
                    </div>
                </div>

                <x-charts.trend :series="$revenueSeries" :labels="$labels" :height="240" />
            </div>
        </div>

        {{-- KOMPOSISI KAMAR --}}
        <div class="card overflow-hidden">
            <div class="card-header bg-stone-50/60">
                <div>
                    <h4 class="section-title">Komposisi Kamar</h4>
                    <p class="mt-0.5 text-xs text-stone-400">Status kamar saat ini</p>
                </div>
            </div>
            <div class="card-body">
                <x-charts.donut :slices="$roomSlices" center-label="Okupansi" :center-value="Chart::percent($occupancy['occupancy_rate'])"
                    empty-text="Belum ada data kamar." />

                <div class="mt-5 rounded-xl bg-stone-50 p-4">
                    <p class="text-xs font-medium text-stone-500">Potensi Pendapatan Sewa</p>
                    <p class="mt-0.5 text-xl font-extrabold tabular text-stone-900">
                        {{ Chart::rupiah($occupancy['occupied_revenue']) }}</p>
                    <p class="mt-1 text-[11px] text-stone-400">Dari {{ $occupancy['occupied_rooms'] }} kamar terisi</p>
                </div>
            </div>
        </div>

        {{-- TREN OKUPANSI --}}
        <div class="card overflow-hidden lg:col-span-2">
            <div class="card-header bg-stone-50/60">
                <div>
                    <h4 class="section-title">Tren Okupansi</h4>
                    <p class="mt-0.5 text-xs text-stone-400">Persentase kamar terisi tiap bulan</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-extrabold tabular text-brand-700">
                        {{ Chart::percent($occupancy['occupancy_rate']) }}</p>
                    <p class="text-[11px] text-stone-400">saat ini</p>
                </div>
            </div>
            <div class="card-body">
                <x-charts.trend :series="$occupancySeries" :labels="array_column($occupancy['points'], 'label')" format="percent" :height="220" />
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/charts.js')
@endpush
