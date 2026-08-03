<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Laporan Pembayaran') }}
        </h2>
    </x-slot>

    <div class="page-container pt-4 sm:pt-5 pb-8 sm:pb-10">

        <form method="GET" action="{{ route('reports.payments') }}">
            <x-filter-panel reset="{{ route('reports.payments') }}">
                <x-filter-date name="start_date" label="Tanggal Mulai" />
                <x-filter-date name="end_date" label="Tanggal Akhir" />
                <x-filter-select name="status" label="Status" :options="['' => 'Semua Status', 'paid' => 'Lunas', 'pending' => 'Pending', 'overdue' => 'Overdue']" />
            </x-filter-panel>
        </form>

        <!-- Summary Cards -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="card p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-stone-500">Total Pembayaran</p>
                        <p class="mt-1 text-2xl font-extrabold text-stone-900 tabular">{{ $payments->count() }}</p>
                    </div>
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
            </div>
            <div class="card p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-stone-500">Total Pendapatan</p>
                        <p class="mt-1 text-2xl font-extrabold text-emerald-600 tabular">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                </div>
            </div>
            <div class="card p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-stone-500">Lunas</p>
                        <p class="mt-1 text-2xl font-extrabold text-emerald-600 tabular">{{ $payments->where('status', 'paid')->count() }}</p>
                    </div>
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
            <div class="card p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-stone-500">Pending / Overdue</p>
                        <p class="mt-1 text-2xl font-extrabold text-amber-600 tabular">{{ $payments->where('status', '!=', 'paid')->count() }}</p>
                    </div>
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h3 class="section-title">Detail Laporan</h3>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('reports.payments', array_merge(request()->all(), ['download' => 'pdf'])) }}"
                    class="btn-danger btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19h6m-6-4h6"/></svg>
                    Download PDF
                </a>
                <button data-print class="btn-secondary btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-max w-full">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Invoice</th>
                            <th>Penghuni</th>
                            <th>Kamar</th>
                            <th>Periode</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $index => $payment)
                        <tr>
                            <td class="text-stone-500">{{ $index + 1 }}</td>
                            <td>
                                {{ $payment->payment_date->format('d/m/Y') }}
                            </td>
                            <td class="font-medium text-stone-900 tabular">
                                {{ $payment->invoice_number }}
                            </td>
                            <td>
                                <span class="font-medium text-stone-900">{{ $payment->tenant->name }}</span>
                            </td>
                            <td>
                                {{ $payment->room->room_number }}
                            </td>
                            <td>
                                {{ $payment->period_month->format('M Y') }}
                            </td>
                            <td class="font-semibold text-stone-900 tabular">
                                Rp {{ number_format($payment->total, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge
                                    @if($payment->status == 'paid') badge-success
                                    @elseif($payment->status == 'pending') badge-warning
                                    @elseif($payment->status == 'overdue') badge-danger
                                    @else badge-neutral @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10">
                                <div class="empty-state">
                                    <svg class="h-10 w-10 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="mt-3 text-sm font-semibold text-stone-500">Tidak ada data pembayaran</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-right font-bold text-stone-900">TOTAL:</td>
                            <td colspan="2" class="text-lg font-extrabold text-emerald-600 tabular">
                                Rp {{ number_format($totalAmount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
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
