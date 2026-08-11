<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="page-title">
                {{ __('Pembayaran') }}
            </h2>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('payments.create') }}" class="btn-primary btn-sm flex-1 sm:flex-none">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="page-container pt-4 sm:pt-5 pb-8 sm:pb-10">

        @if(session('success'))
        <div class="alert-success mb-4">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @php
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $monthOptions = ['' => 'Semua'] + $months;
            $yearOptions = ['' => 'Semua'];
            for ($y = date('Y') + 1; $y >= date('Y') - 3; $y--) { $yearOptions[$y] = $y; }
            $tenantOptions = ['' => 'Semua Penghuni'];
            foreach ($tenants as $t) { $tenantOptions[$t->id] = $t->name; }
        @endphp

        <form method="GET" action="{{ route('payments.index') }}">
            <x-filter-panel reset="{{ route('payments.index') }}">
                <x-filter-select name="filter_month" label="Bulan" :options="$monthOptions" />
                <x-filter-select name="filter_year" label="Tahun" :options="$yearOptions" />
                <x-filter-select name="tenant_id" label="Penghuni" :options="$tenantOptions" />
                <x-filter-input name="invoice_number" label="Invoice" placeholder="Cari No. Invoice..." />
                <x-filter-select name="status" label="Status" :options="['paid' => 'Lunas', 'pending' => 'Pending', 'overdue' => 'Overdue', 'cancelled' => 'Batal']" />
            </x-filter-panel>
        </form>

        <div class="card overflow-hidden">
            <div class="p-5 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-max w-full">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th class="hidden sm:table-cell">Penghuni</th>
                                <th class="hidden md:table-cell">Kamar</th>
                                <th>Tanggal Bayar</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td>
                                    <span class="font-medium text-stone-900">{{ $payment->invoice_number }}</span>
                                    <div class="text-[10px] text-stone-400 sm:hidden">
                                        {{ $payment->tenant->name }} (Rm {{ $payment->room->room_number }})
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="font-medium text-stone-900">{{ $payment->tenant->name }}</span>
                                </td>
                                <td class="hidden md:table-cell">
                                    {{ $payment->room->room_number }}
                                </td>
                                <td>
                                    {{ $payment->payment_date->format('d M Y') }}
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
                                <td>
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- FITUR WHATSAPP --}}
                                        @if($payment->tenant->phone)
                                            <button type="button"
                                                    data-send-wa="{{ $payment->uuid }}"
                                                    data-tenant-name="{{ $payment->tenant->name }}"
                                                    class="rounded-lg bg-emerald-500 p-2 text-white shadow-soft transition hover:bg-emerald-600">
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                            </button>
                                        @endif

                                        <a href="{{ route('payments.show', $payment) }}" class="p-1.5 text-stone-400 transition hover:text-brand-600" title="Detail">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="p-1.5 text-stone-400 transition hover:text-emerald-600" title="Kwitansi">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>

                                        <a href="{{ route('payments.edit', $payment) }}" class="p-1.5 text-stone-400 transition hover:text-brand-600" title="Edit">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <form id="delete-payment-{{ $payment->id }}" action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                data-confirm-delete="delete-payment-{{ $payment->id }}"
                                                data-item-name="{{ $payment->invoice_number }}"
                                                class="p-1.5 text-stone-400 transition hover:text-red-600" title="Hapus">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10">
                                    <div class="empty-state">
                                        <svg class="h-10 w-10 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="mt-3 text-sm font-semibold text-stone-500">Data tidak ditemukan untuk filter ini.</p>
                                        <p class="mt-1 text-xs text-stone-400">Coba ubah kriteria pencarian atau reset filter.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$payments" />
            </div>
        </div>

    </div>

    @if(session('new_payment_id'))
        @php
            $newPayment = \App\Models\Payment::with('tenant')->find(session('new_payment_id'));
        @endphp
        @if($newPayment)
            <div id="new-payment-data" data-uuid="{{ $newPayment->uuid }}" data-name="{{ $newPayment->tenant->name }}" class="hidden"></div>
        @endif
    @endif

    @push('scripts')
        @vite('resources/js/pages/payments-index.js')
    @endpush
</x-app-layout>
