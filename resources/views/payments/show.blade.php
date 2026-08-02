<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="page-title">
                {{ __('Detail Pembayaran') }}
            </h2>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('payments.receipt', $payment) }}" class="btn-success btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download Kwitansi
                </a>
                <a href="{{ route('payments.edit', $payment) }}" class="btn-primary btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <a href="{{ route('payments.index') }}" class="btn-secondary btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="page-container pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="mx-auto max-w-4xl">

            <!-- Invoice Header -->
            <div class="card animate-fade-in-up">
                <div class="card-body p-6 sm:p-8">
                    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h3 class="text-3xl font-extrabold tracking-tight text-stone-900">Invoice</h3>
                            <p class="mt-1 text-sm text-stone-500 tabular">{{ $payment->invoice_number }}</p>
                        </div>
                        <div class="text-right">
                            <span class="badge
                                @if($payment->status == 'paid') badge-success
                                @elseif($payment->status == 'pending') badge-warning
                                @elseif($payment->status == 'overdue') badge-danger
                                @else badge-neutral @endif">
                                {{ strtoupper($payment->status) }}
                            </span>
                            <p class="mt-2 text-sm text-stone-500">
                                {{ $payment->payment_date->format('d F Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-6 border-t border-stone-100 pt-6 sm:grid-cols-2">
                        <!-- Informasi Penghuni -->
                        <div>
                            <h4 class="section-title mb-3">Informasi Penghuni</h4>
                            <div class="flex items-start gap-4">
                                @if($payment->tenant->photo)
                                <img src="{{ asset('storage/' . $payment->tenant->photo) }}"
                                        class="h-16 w-16 rounded-full object-cover ring-4 ring-brand-50"
                                        alt="{{ $payment->tenant->name }}">
                                @else
                                <div class="avatar h-16 w-16 text-xl">
                                    <span>{{ substr($payment->tenant->name, 0, 1) }}</span>
                                </div>
                                @endif
                                <div>
                                    <p class="font-bold text-stone-900">{{ $payment->tenant->name }}</p>
                                    <p class="text-sm text-stone-500">{{ $payment->tenant->email }}</p>
                                    <p class="text-sm text-stone-500">{{ $payment->tenant->phone }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kamar -->
                        <div>
                            <h4 class="section-title mb-3">Informasi Kamar</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-stone-500">Nomor Kamar:</span>
                                    <span class="font-semibold text-stone-900">{{ $payment->room->room_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-stone-500">Tipe:</span>
                                    <span class="font-semibold text-stone-900">{{ ucfirst($payment->room->type) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-stone-500">Harga Sewa:</span>
                                    <span class="font-semibold text-stone-900 tabular">Rp {{ number_format($payment->room->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Pembayaran -->
                    <div class="border-t border-stone-100 pt-6">
                        <h4 class="section-title mb-4">Detail Pembayaran</h4>
                        <div class="space-y-3 rounded-xl bg-stone-50 p-5">
                            <div class="flex justify-between text-sm">
                                <span class="text-stone-500">Periode:</span>
                                <span class="font-semibold text-stone-900">{{ $payment->period_month->format('F Y') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-stone-500">Tanggal Pembayaran:</span>
                                <span class="font-semibold text-stone-900">{{ $payment->payment_date->format('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-stone-500">Metode Pembayaran:</span>
                                <span class="font-semibold text-stone-900">{{ ucfirst(str_replace('-', ' ', $payment->payment_method ?? '-')) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Biaya -->
                    <div class="mt-6 space-y-3 border-t border-stone-100 pt-6">
                        <div class="flex justify-between">
                            <span class="text-stone-500">Biaya Sewa:</span>
                            <span class="font-semibold text-stone-900 tabular">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                        @if($payment->late_fee > 0)
                        <div class="flex justify-between">
                            <span class="text-stone-500">Denda Keterlambatan:</span>
                            <span class="font-semibold text-red-600 tabular">Rp {{ number_format($payment->late_fee, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between border-t border-stone-100 pt-4">
                            <span class="text-xl font-extrabold text-stone-900">TOTAL:</span>
                            <span class="text-xl font-extrabold text-brand-600 tabular sm:text-2xl">Rp {{ number_format($payment->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Catatan -->
                    @if($payment->notes)
                    <div class="mt-6 border-t border-stone-100 pt-6">
                        <h4 class="section-title mb-2">Catatan:</h4>
                        <p class="rounded-xl bg-stone-50 p-4 text-stone-600">{{ $payment->notes }}</p>
                    </div>
                    @endif

                    <!-- Bukti Pembayaran -->
                    @if($payment->receipt_file)
                    <div class="mt-6 border-t border-stone-100 pt-6">
                        <h4 class="section-title mb-3">Bukti Pembayaran:</h4>
                        <div class="rounded-xl bg-stone-50 p-4">
                            @if(Str::endsWith($payment->receipt_file, '.pdf'))
                                <a href="{{ asset('storage/' . $payment->receipt_file) }}"
                                    target="_blank"
                                    class="btn-danger btn-sm">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Lihat PDF
                                </a>
                            @else
                                <img src="{{ asset('storage/' . $payment->receipt_file) }}"
                                        alt="Bukti Pembayaran"
                                        class="max-w-md rounded-xl border border-stone-200">
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Timestamp -->
                    <div class="mt-6 flex flex-wrap justify-between gap-2 border-t border-stone-100 pt-4 text-xs text-stone-400">
                        <span>Dibuat: {{ $payment->created_at->format('d F Y H:i') }}</span>
                        <span>Diupdate: {{ $payment->updated_at->format('d F Y H:i') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
