<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="page-title">
                {{ __('Detail Penghuni') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('tenants.edit', $tenant) }}" class="btn-primary btn-sm">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <a href="{{ route('tenants.index') }}" class="btn-secondary btn-sm">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="page-container">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Personal Info -->
                    <div class="card">
                        <div class="card-body">
                            <div class="flex items-start justify-between mb-6">
                                <div class="flex items-center gap-4">
                                    @if($tenant->photo)
                                    <img src="{{ asset('storage/' . $tenant->photo) }}"
                                        alt="{{ $tenant->name }}"
                                        class="h-24 w-24 rounded-full object-cover border-4 border-brand-100">
                                    @else
                                    <div class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-brand-100 bg-brand-100">
                                        <span class="text-3xl font-bold text-brand-700">{{ substr($tenant->name, 0, 1) }}</span>
                                    </div>
                                    @endif
                                    <div>
                                        <h3 class="text-2xl font-bold text-stone-900">{{ $tenant->name }}</h3>
                                        <p class="text-stone-500">{{ $tenant->email }}</p>
                                        <span class="mt-2 inline-flex {{ $tenant->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ ucfirst($tenant->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-stone-100 pt-6">
                                <div>
                                    <p class="text-sm text-stone-500">No. Telepon</p>
                                    <p class="font-semibold text-stone-900 tabular">{{ $tenant->phone }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-stone-500">No. KTP/ID</p>
                                    <p class="font-semibold text-stone-900 tabular">{{ $tenant->id_card }}</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <p class="text-sm text-stone-500">Alamat</p>
                                    <p class="font-semibold text-stone-900">{{ $tenant->address }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-stone-500">Tanggal Masuk</p>
                                    <p class="font-semibold text-stone-900 tabular">{{ $tenant->entry_date->format('d F Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-stone-500">Tanggal Keluar</p>
                                    <p class="font-semibold text-stone-900 tabular">{{ $tenant->exit_date ? $tenant->exit_date->format('d F Y') : '-' }}</p>
                                </div>
                                <div class="sm:col-span-2 mt-2">
                                    <p class="text-sm text-stone-500 mb-2">Kontak Darurat</p>

                                    @if($tenant->emergency_contact_name || $tenant->emergency_contact_phone)
                                        <div class="flex justify-between items-center gap-3 rounded-xl border border-red-100 bg-red-50 p-3">
                                            <div>
                                                <p class="font-bold text-stone-900">{{ $tenant->emergency_contact_name ?? '-' }}</p>
                                                <p class="text-sm text-stone-700 tabular">{{ $tenant->emergency_contact_phone ?? '-' }}</p>
                                            </div>
                                            @if($tenant->emergency_contact_phone)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tenant->emergency_contact_phone) }}"
                                                target="_blank"
                                                class="btn-success btn-sm shrink-0">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                                    Hubungi
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <p class="font-semibold text-stone-400 italic text-sm">Data kontak darurat tidak tersedia</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment History -->
                    <div class="card">
                        <div class="card-body">
                            <h3 class="section-title mb-4">Riwayat Pembayaran</h3>
                            @if($tenant->payments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-max w-full">
                                    <thead>
                                        <tr>
                                            <th>Periode</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tenant->payments->take(10) as $payment)
                                        <tr>
                                            <td class="font-medium text-stone-900">{{ $payment->period_month->format('M Y') }}</td>
                                            <td class="text-stone-500 tabular">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                            <td class="font-semibold text-stone-900 tabular">Rp {{ number_format($payment->total, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="{{ $payment->status == 'paid' ? 'badge-success' : 'badge-warning' }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-center text-sm text-stone-400 py-6">Belum ada riwayat pembayaran</p>
                            @endif
                        </div>
                    </div>

                    <!-- Complaints History -->
                    <div class="card">
                        <div class="card-body">
                            <h3 class="section-title mb-4">Riwayat Keluhan</h3>
                            @if($tenant->complaints->count() > 0)
                            <div class="space-y-3">
                                @foreach($tenant->complaints as $complaint)
                                <div class="rounded-xl border border-stone-100 bg-stone-50/60 p-4">
                                    <div class="flex flex-wrap justify-between items-start gap-2 mb-2">
                                        <h4 class="font-semibold text-stone-900">{{ $complaint->title }}</h4>
                                        <span class="{{ $complaint->status == 'resolved' ? 'badge-success' : 'badge-warning' }}">
                                            {{ ucfirst($complaint->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-stone-600 mb-3">{{ $complaint->description }}</p>
                                    <div class="flex flex-wrap justify-between items-center gap-2 text-xs text-stone-500">
                                        <span class="tabular">{{ $complaint->created_at->format('d M Y') }}</span>
                                        <span class="badge-neutral">{{ ucfirst($complaint->category) }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-center text-sm text-stone-400 py-6">Belum ada keluhan</p>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- Sidebar -->
                <div class="space-y-6">

                    <!-- Room Info -->
                    <div class="card">
                        <div class="card-body">
                            <h3 class="section-title mb-4">Informasi Kamar</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-stone-500">Nomor Kamar</p>
                                    <p class="text-xl font-bold text-brand-600">{{ $tenant->room->room_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-stone-500">Tipe</p>
                                    <p class="font-semibold text-stone-900">{{ ucfirst($tenant->room->type) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-stone-500">Harga Sewa</p>
                                    <p class="font-semibold text-stone-900 tabular">Rp {{ number_format($tenant->room->price, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-stone-500">Ukuran</p>
                                    <p class="font-semibold text-stone-900">{{ $tenant->room->size ? $tenant->room->size . ' m²' : '-' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('rooms.show', $tenant->room) }}" class="btn-primary mt-5 w-full justify-center">
                                Lihat Detail Kamar
                            </a>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="card">
                        <div class="card-body">
                            <h3 class="section-title mb-4">Statistik</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-stone-500">Total Pembayaran</span>
                                    <span class="font-semibold text-stone-900 tabular">{{ $tenant->payments->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-stone-500">Total Pengeluaran</span>
                                    <span class="font-semibold text-emerald-600 tabular">Rp {{ number_format($tenant->payments->sum('total'), 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-stone-500">Total Keluhan</span>
                                    <span class="font-semibold text-stone-900 tabular">{{ $tenant->complaints->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-stone-500">Lama Tinggal</span>
                                    <span class="font-semibold text-stone-900 text-right tabular">
                                        @php
                                            $start = $tenant->entry_date;

                                            if ($tenant->status === 'inactive') {
                                                $end = $tenant->exit_date ?? now();
                                            } else {
                                                $end = now();
                                            }

                                            $diff = $start->diff($end);
                                        @endphp

                                        @if($diff->y > 0 || $diff->m > 0)
                                            {{ $diff->y > 0 ? $diff->y . ' Tahun ' : '' }}
                                            {{ $diff->m > 0 ? $diff->m . ' Bulan ' : '' }}
                                            {{ $diff->d > 0 ? $diff->d . ' Hari' : '' }}
                                        @else
                                            {{ $diff->d }} Hari
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
