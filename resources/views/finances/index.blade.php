<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="page-title">
                {{ __('Pencatatan Keuangan') }}
            </h2>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <a href="{{ route('finances.dashboard') }}"
                    class="btn-secondary btn-sm flex-1 sm:flex-none">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="hidden sm:inline">Dashboard</span>
                </a>
                <a href="{{ route('finances.report') }}"
                    class="btn-success btn-sm flex-1 sm:flex-none">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="hidden sm:inline">Laporan</span>
                </a>
                <a href="{{ route('finances.create') }}"
                    class="btn-primary btn-sm flex-1 sm:flex-none">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="page-container pt-4 sm:pt-5 pb-8 sm:pb-10" x-data="{ openDetail: false, selectedReceipt: {} }">

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="card p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-stone-500">Total Pemasukan</p>
                        <p class="mt-1 text-2xl font-extrabold text-emerald-600 tabular">Rp
                            {{ number_format($totalIncome, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-stone-500">Total Pengeluaran</p>
                        <p class="mt-1 text-2xl font-extrabold text-red-600 tabular">Rp
                            {{ number_format($totalExpense, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 12H4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-stone-500">Saldo</p>
                        <p class="mt-1 text-2xl font-extrabold tabular {{ $balance >= 0 ? 'text-brand-600' : 'text-orange-600' }}">
                            Rp {{ number_format($balance, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl {{ $balance >= 0 ? 'bg-brand-100 text-brand-600' : 'bg-orange-100 text-orange-600' }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET">
            <x-filter-panel reset="{{ route('finances.index') }}">
                <div class="lg:col-span-4" x-data="dateRangePicker" data-start-date="{{ $startDate }}" data-end-date="{{ $endDate }}">
                    <div class="mb-2 text-[11px] font-bold uppercase tracking-wider text-stone-400">Rentang Tanggal</div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-stone-400">Dari</label>
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                @change="endDate = null" x-model="startDate"
                                class="w-full rounded-xl border-stone-200 bg-white px-3 py-2 text-sm shadow-sm transition focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-stone-400">Sampai</label>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                x-model="endDate" :min="startDate" :disabled="!startDate"
                                :class="!startDate && 'opacity-50'"
                                class="w-full rounded-xl border-stone-200 bg-white px-3 py-2 text-sm shadow-sm transition focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div class="lg:col-span-2">
                            <div class="mb-1.5 text-[11px] font-bold uppercase tracking-wider text-stone-400">Quick</div>
                            <div class="flex flex-wrap gap-1.5">
                                <button @click.prevent="setRange(0, 6)" type="button" class="rounded-lg bg-stone-100 px-3 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-200">7 hari</button>
                                <button @click.prevent="setRange(0, 29)" type="button" class="rounded-lg bg-stone-100 px-3 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-200">30 hari</button>
                                <button @click.prevent="setRangeMonth()" type="button" class="rounded-lg bg-stone-100 px-3 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-200">Bulan ini</button>
                                <button @click.prevent="setRangeMonth(-1)" type="button" class="rounded-lg bg-stone-100 px-3 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-200">Bulan lalu</button>
                            </div>
                        </div>
                    </div>
                </div>
                <x-filter-select name="type" label="Tipe" :options="['' => 'Semua', 'income' => 'Pemasukan', 'expense' => 'Pengeluaran']" />
                <x-filter-input name="category" label="Kategori" placeholder="Nama kategori" />
            </x-filter-panel>
        </form>

        <div class="card overflow-hidden">
            <div class="p-5 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-max w-full">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($finances as $finance)
                                <tr>
                                    <td>
                                        {{ $finance->transaction_date->format('d M Y') }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $finance->type == 'income' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $finance->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-medium text-stone-900">{{ $finance->category }}</span>
                                    </td>
                                    <td class="max-w-xs whitespace-normal">
                                        {{ Str::limit($finance->description, 50) }}
                                    </td>
                                    <td class="font-semibold tabular {{ $finance->type == 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $finance->type == 'income' ? '+' : '-' }} Rp
                                        {{ number_format($finance->amount, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-1">

                                            @if ($finance->type == 'income' && $finance->payment && $finance->payment->receipt_file)
                                                <button type="button"
                                                    @click="
                                                        selectedReceipt = {
                                                            invoice_number: '{{ $finance->payment->invoice_number ?? 'INV-' . date('Ymd') . '-' . $finance->id }}',
                                                            file_url: '{{ asset('storage/' . $finance->payment->receipt_file) }}'
                                                        };
                                                        openDetail = true;
                                                    "
                                                    class="rounded-lg p-1.5 text-stone-400 transition hover:bg-brand-50 hover:text-brand-600"
                                                    title="Lihat Berkas Kwitansi">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </button>
                                            @endif

                                            <a href="{{ route('finances.show', $finance) }}"
                                                class="rounded-lg p-1.5 text-stone-400 transition hover:bg-brand-50 hover:text-brand-600"
                                                title="Detail Transaksi">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('finances.edit', $finance) }}"
                                                class="rounded-lg p-1.5 text-stone-400 transition hover:bg-brand-50 hover:text-brand-600"
                                                title="Edit Transaksi">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <form id="delete-finance-{{ $finance->id }}"
                                                action="{{ route('finances.destroy', $finance) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button
                                                data-confirm-delete="delete-finance-{{ $finance->id }}"
                                                data-item-name="Transaksi ini"
                                                class="rounded-lg p-1.5 text-stone-400 transition hover:bg-red-50 hover:text-red-600"
                                                title="Hapus Transaksi">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10">
                                        <div class="empty-state">
                                            <svg class="h-10 w-10 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="mt-3 text-sm font-semibold text-stone-500">Belum ada transaksi keuangan</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $finances->links() }}
                </div>
            </div>
        </div>

        <div class="fixed inset-0 z-50 overflow-y-auto" x-show="openDetail"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
            <div class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm transition-opacity"
                @click="openDetail = false"></div>
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative w-full transform overflow-hidden rounded-3xl border border-stone-100 bg-white text-left shadow-2xl transition-all sm:my-8 sm:max-w-xl"
                    @click.away="openDetail = false">
                    <div class="flex items-center justify-between border-b border-stone-100 bg-stone-50 px-6 py-4">
                        <div>
                            <span
                                class="rounded-md bg-brand-100 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-brand-800">Serrata
                                Kost</span>
                            <h3 class="mt-0.5 flex items-center gap-2 text-sm font-black text-stone-800">
                                <span>Detail Kwitansi</span>
                                <span
                                    class="rounded-md border border-stone-300/60 bg-stone-200/60 px-2 py-0.5 font-mono text-xs tracking-wider text-stone-600 shadow-inner"
                                    x-text="selectedReceipt.invoice_number"></span>
                            </h3>
                        </div>
                        <button type="button" @click="openDetail = false"
                            class="text-2xl font-bold text-stone-400 hover:text-stone-600">&times;</button>
                    </div>
                    <div class="bg-white p-6">
                        <div
                            class="w-full overflow-hidden rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-2 shadow-inner">
                            <div
                                class="flex h-96 w-full items-center justify-center overflow-auto rounded-xl bg-white">
                                <img :src="selectedReceipt.file_url"
                                    class="mx-auto max-h-full max-w-full object-contain" />
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between border-t border-stone-100 bg-stone-50 px-6 py-4">
                        <a :href="selectedReceipt.file_url" download
                            class="btn-primary btn-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download Kwitansi
                        </a>
                        <button type="button" @click="openDetail = false"
                            class="btn-secondary btn-sm">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

