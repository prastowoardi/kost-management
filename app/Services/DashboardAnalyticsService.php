<?php

namespace App\Services;

use App\Models\Finance;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Menghitung metrik analitik untuk dashboard admin:
 * tren pemasukan/pengeluaran, ageing tunggakan (arrears), dan okupansi kamar.
 *
 * Seluruh perhitungan dilakukan dengan query yang portable (tidak memakai
 * fungsi tanggal spesifik MySQL seperti DATE_FORMAT) agar unit test yang
 * berjalan di SQLite menghasilkan angka yang sama dengan produksi.
 */
class DashboardAnalyticsService
{
    public const CACHE_KEY = 'dashboard.analytics';

    public const CACHE_TTL_MINUTES = 15;

    /**
     * Ambang batas periode tunggakan yang ditelusuri per penghuni.
     * Makin dalam, makin akurat ageing, tapi rentang agregasi makin lebar.
     */
    public const MAX_LOOKBACK_MONTHS = 6;

    /**
     * Bucket ageing tunggakan. `max_days` menentukan batas atas tiap bucket.
     */
    public const AGING_BUCKETS = [
        ['key' => 'not_due', 'label' => 'Belum Jatuh Tempo', 'max_days' => 0],
        ['key' => 'd1_7', 'label' => '1-7 Hari', 'max_days' => 7],
        ['key' => 'd8_15', 'label' => '8-15 Hari', 'max_days' => 15],
        ['key' => 'd16_30', 'label' => '16-30 Hari', 'max_days' => 30],
        ['key' => 'd30_plus', 'label' => '> 30 Hari', 'max_days' => PHP_INT_MAX],
    ];

    /**
     * Singkatan bulan Bahasa Indonesia. Dipakai langsung (bukan translatedFormat)
     * supaya label chart tidak ikut berubah saat locale aplikasi diganti.
     */
    private const MONTH_SHORT = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agu',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des',
    ];

    /**
     * Susun satu payload analitik lengkap untuk dashboard.
     */
    public function snapshot(int $months = 6, bool $cached = true): array
    {
        $months = $this->normalizeMonths($months);

        $resolve = fn(): array => [
            'months' => $months,
            'revenue' => $this->revenueTrend($months),
            'arrears' => $this->arrearsAging(),
            'occupancy' => $this->occupancyTrend($months),
        ];

        if (! $cached) {
            return $resolve();
        }

        return Cache::remember(
            $this->cacheKey($months),
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            $resolve
        );
    }

    /**
     * Cache per-resolusi, supaya switcher 3/6/12 bulan tidak saling menimpa.
     */
    public function cacheKey(int $months): string
    {
        return self::CACHE_KEY . ':' . $this->normalizeMonths($months);
    }

    public function clearCache(): void
    {
        foreach ([3, 6, 12] as $months) {
            Cache::forget($this->cacheKey($months));
        }
    }

    public function normalizeMonths(int $months): int
    {
        return in_array($months, [3, 6, 12], true) ? $months : 6;
    }

    /**
     * Tren pemasukan, pengeluaran, dan saldo untuk N bulan terakhir.
     * Pengelompokan per bulan dilakukan di PHP (bukan SQL) supaya query
     * tetap berjalan di MySQL maupun SQLite.
     *
     * @return array<int, array{key: string, label: string, income: int, expense: int, balance: int}>
     */
    public function revenueTrend(int $months = 6): array
    {
        $months = $this->normalizeMonths($months);

        $periods = $this->periodKeys($months);

        $totals = Finance::query()
            ->whereBetween('transaction_date', [
                $periods[0]['date']->copy()->startOfMonth()->toDateString(),
                now()->toDateString(),
            ])
            ->get(['type', 'amount', 'transaction_date'])
            ->groupBy(fn(Finance $finance) => Carbon::parse($finance->transaction_date)->format('Y-m'))
            ->map(function (Collection $rows): array {
                $income = 0;
                $expense = 0;

                foreach ($rows as $row) {
                    if ($row->type === 'income') {
                        $income += (int) $row->amount;
                    } else {
                        $expense += (int) $row->amount;
                    }
                }

                return ['income' => $income, 'expense' => $expense];
            });

        $trend = [];

        foreach ($periods as $period) {
            $bucket = $totals->get($period['key'], ['income' => 0, 'expense' => 0]);

            $trend[] = [
                'key' => $period['key'],
                'label' => $period['label'],
                'income' => $bucket['income'],
                'expense' => $bucket['expense'],
                'balance' => $bucket['income'] - $bucket['expense'],
            ];
        }

        return $trend;
    }

    /**
     * Ageing tunggakan per penghuni aktif.
     *
     * Setiap penghuni ditelusuri mundur periode demi periode; selama satu
     * periode masih ada sisa tagihan, periode itu dihitung sebagai tunggakan
     * dan penelusuran berlanjut ke bulan sebelumnya. Durasi tunggakan memakai
     * jatuh tempo periode **paling lama** yang belum dibayar, bukan hanya
     * siklus berjalan — inilah bedanya dengan sekadar membaca `days_left`.
     *
     * @return array<string, mixed>
     */
    public function arrearsAging(): array
    {
        $tenants = Tenant::query()
            ->with('room:id,room_number,price')
            ->where('status', 'active')
            ->whereNotNull('entry_date')
            ->get()
            ->filter(fn(Tenant $tenant) => $tenant->calculated_due_date !== null);

        $paid = $this->paidAmountsPerPeriod($tenants->pluck('id')->all());

        $rows = $tenants
            ->map(fn(Tenant $tenant) => $this->arrearsForTenant($tenant, $paid))
            ->filter(fn(?array $row) => $row !== null && $row['outstanding'] > 0)
            ->sortByDesc('outstanding')
            ->values();

        $buckets = array_map(fn(array $bucket): array => [
            'key' => $bucket['key'],
            'label' => $bucket['label'],
            'count' => 0,
            'amount' => 0,
        ], self::AGING_BUCKETS);

        $indexes = array_flip(array_column(self::AGING_BUCKETS, 'key'));

        foreach ($rows as $row) {
            $index = $indexes[$row['bucket']];

            $buckets[$index]['count']++;
            $buckets[$index]['amount'] += $row['outstanding'];
        }

        // "Belum Jatuh Tempo" bukan tunggakan, jadi tidak ikut dijumlahkan.
        $overdue = array_slice($buckets, 1);

        return [
            'buckets' => $buckets,
            'total_outstanding' => (int) $rows->sum('outstanding'),
            'total_tenants' => $rows->count(),
            'overdue_count' => (int) array_sum(array_column($overdue, 'count')),
            'overdue_amount' => (int) array_sum(array_column($overdue, 'amount')),
            'top_debtors' => $rows->take(5)->all(),
        ];
    }

    /**
     * Okupansi kamar: komposisi saat ini + tren taux okupansi N bulan terakhir.
     *
     * Okupansi historis dihitung dari `entry_date`/`exit_date` tenant dengan
     * `distinct room_id`, jadi satu kamar yang sempat disewai dua kali dalam
     * sebulan tetap terhitung satu kamar. Karena riwayat kamar sendiri tidak
     * disimpan, penyebut memakai jumlah kamar saat ini.
     *
     * @return array<string, mixed>
     */
    public function occupancyTrend(int $months = 6): array
    {
        $months = $this->normalizeMonths($months);

        $statusCounts = Room::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn($value) => (int) $value);

        $totalRooms = (int) $statusCounts->sum();
        $occupied = (int) $statusCounts->get('occupied', 0);

        $points = [];

        foreach ($this->periodKeys($months) as $period) {
            $monthEnd = $period['date']->copy()->endOfMonth();

            $occupiedThen = Tenant::query()
                ->whereNotNull('room_id')
                ->whereDate('entry_date', '<=', $monthEnd->toDateString())
                ->where(fn($query) => $query
                    ->whereNull('exit_date')
                    ->orWhereDate('exit_date', '>=', $monthEnd->toDateString()))
                ->distinct()
                ->count('room_id');

            $points[] = [
                'key' => $period['key'],
                'label' => $period['label'],
                'occupied' => $occupiedThen,
                'vacant' => max(0, $totalRooms - $occupiedThen),
                'rate' => $totalRooms > 0 ? round($occupiedThen / $totalRooms * 100, 1) : 0.0,
                'is_current' => $period['key'] === Carbon::now()->format('Y-m'),
            ];
        }

        return [
            'total_rooms' => $totalRooms,
            'occupied_rooms' => $occupied,
            'available_rooms' => (int) $statusCounts->get('available', 0),
            'maintenance_rooms' => (int) $statusCounts->get('maintenance', 0),
            'occupancy_rate' => $totalRooms > 0 ? round($occupied / $totalRooms * 100, 1) : 0.0,
            'occupied_revenue' => (int) Room::query()->where('status', 'occupied')->sum('price'),
            'points' => $points,
        ];
    }

    /**
     * Total `total` per penghuni per periode (Y-m) untuk pembayaran berstatus paid.
     *
     * @param  array<int, int|string>  $tenantIds
     * @return Collection<string, int>
     */
    private function paidAmountsPerPeriod(array $tenantIds): Collection
    {
        if ($tenantIds === []) {
            return collect();
        }

        return Payment::query()
            ->whereIn('tenant_id', $tenantIds)
            ->where('status', 'paid')
            ->get(['tenant_id', 'period_month', 'total'])
            ->groupBy(fn(Payment $payment) => $payment->tenant_id . '|' . Carbon::parse($payment->period_month)->format('Y-m'))
            ->map(fn(Collection $rows) => (int) $rows->sum('total'));
    }

    /**
     * Hitung tunggakan satu penghuni dengan menelusuri periode ke belakang.
     *
     * @param  Collection<string, int>  $paid
     * @return array<string, mixed>|null
     */
    private function arrearsForTenant(Tenant $tenant, Collection $paid): ?array
    {
        $price = (float) ($tenant->room?->price ?? 0);

        if ($price <= 0) {
            return null;
        }

        $entryDay = (int) Carbon::parse($tenant->entry_date)->day;
        $period = Carbon::now()->copy()->startOfMonth();
        $today = Carbon::now()->startOfDay();

        $outstanding = 0.0;
        $unpaidMonths = 0;
        $oldestDueDate = null;
        $unpaidPeriods = [];

        for ($i = 0; $i < self::MAX_LOOKBACK_MONTHS; $i++) {
            $dueDate = Carbon::create(
                $period->year,
                $period->month,
                min($entryDay, $period->daysInMonth)
            )->startOfDay();

            $paidAmount = (float) $paid->get($tenant->id . '|' . $period->format('Y-m'), 0);
            $remaining = max(0.0, $price - $paidAmount);

            // Periode ini lunas, jadi periode sebelumnya dianggap sudah lunas juga.
            if ($remaining <= 0) {
                break;
            }

            $outstanding += $remaining;
            $unpaidMonths++;
            $unpaidPeriods[] = $period->format('Y-m');

            // Hanya periode yang sudah lewat jatuh tempo yang jadi hitungan arrears.
            // Penugasan (bukan ??=) disengaja: penelusuran berjalan dari periode
            // terbaru ke terlama, sehingga nilai terakhir = jatuh tempo tertua.
            if ($today->greaterThan($dueDate)) {
                $oldestDueDate = $dueDate;
            }

            $period = $period->copy()->subMonthNoOverflow();
        }

        if ($unpaidMonths === 0) {
            return null;
        }

        // diffInDays dihitung dari jatuh tempo ke hari ini supaya hasilnya
        // selalu positif (= sudah telat berapa hari).
        $overdueDays = $oldestDueDate ? (int) $oldestDueDate->diffInDays($today) : 0;

        return [
            'tenant_id' => $tenant->id,
            'name' => $tenant->name,
            'room_number' => $tenant->room?->room_number ?? '-',
            'outstanding' => (int) round($outstanding),
            'unpaid_months' => $unpaidMonths,
            'unpaid_periods' => $unpaidPeriods,
            'overdue_days' => $overdueDays,
            'bucket' => $this->bucketKeyFor($overdueDays),
        ];
    }

    private function bucketKeyFor(int $overdueDays): string
    {
        foreach (self::AGING_BUCKETS as $bucket) {
            if ($overdueDays <= $bucket['max_days']) {
                return $bucket['key'];
            }
        }

        return self::AGING_BUCKETS[count(self::AGING_BUCKETS) - 1]['key'];
    }

    /**
     * N bulan terakhir, dari yang paling lama ke yang paling baru.
     *
     * @return array<int, array{key: string, label: string, date: Carbon}>
     */
    private function periodKeys(int $months): array
    {
        $periods = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonthsNoOverflow($i);

            $periods[] = [
                'key' => $date->format('Y-m'),
                'label' => self::MONTH_SHORT[$date->month] . ' ' . $date->year,
                'date' => $date,
            ];
        }

        return $periods;
    }
}
