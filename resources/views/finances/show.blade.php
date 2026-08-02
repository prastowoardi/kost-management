<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="page-title">
                {{ __('Detail Transaksi Keuangan') }}
            </h2>
        </div>
    </x-slot>

    <div class="page-container pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="mx-auto max-w-4xl">
            <div class="card animate-fade-in-up overflow-hidden">
                <div class="card-body p-5 sm:p-6">

                    {{-- Judul Transaksi --}}
                    <div class="mb-6 flex flex-wrap items-start justify-between gap-3 border-b border-stone-100 pb-4">
                        <div>
                            <h3 class="text-xl font-extrabold tracking-tight text-stone-900 sm:text-2xl">
                                {{ $finance->description }}
                            </h3>
                            <p class="mt-1 text-sm text-stone-500">
                                ID Transaksi: <span class="font-mono tabular">{{ $finance->id }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="badge {{ $finance->type == 'income' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($finance->type) == 'Income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                        </div>
                    </div>

                    {{-- Detail Transaksi --}}
                    <div class="grid grid-cols-1 gap-5 text-stone-700 md:grid-cols-2">

                        {{-- Baris 1: Jumlah dan Kategori --}}
                        <div>
                            <p class="text-sm font-medium text-stone-500">Jumlah Transaksi</p>
                            <p class="mt-1 text-2xl font-extrabold tabular {{ $finance->type == 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                Rp {{ number_format($finance->amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-stone-500">Kategori</p>
                            <p class="mt-1 text-lg font-semibold text-stone-900">{{ $finance->category }}</p>
                        </div>

                        {{-- Baris 2: Tanggal dan Dibuat Pada --}}
                        <div>
                            <p class="text-sm font-medium text-stone-500">Tanggal Transaksi</p>
                            <p class="mt-1 text-base">{{ \Carbon\Carbon::parse($finance->transaction_date)->translatedFormat('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-stone-500">Dicatat Pada</p>
                            <p class="mt-1 text-base">{{ $finance->created_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>

                        {{-- Baris 3: Catatan --}}
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-stone-500">Catatan</p>
                            <div class="mt-2 min-h-[50px] rounded-xl border border-stone-100 bg-stone-50 p-4">
                                <p class="whitespace-pre-wrap text-base">{{ $finance->notes ?? 'Tidak ada catatan.' }}</p>
                            </div>
                        </div>

                        {{-- Baris 4: Bukti Transaksi --}}
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-stone-500">Bukti Transaksi</p>
                            @if($finance->payment && $finance->payment->receipt_file)
                                {{-- CEK JIKA FILE ADALAH GAMBAR --}}
                                @if(in_array(pathinfo($finance->payment->receipt_file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                    <div class="mb-3 mt-2">
                                        <img src="{{ asset('storage/' . $finance->payment->receipt_file) }}"
                                            alt="Bukti Transaksi"
                                            class="max-w-xs rounded-xl border border-stone-200 p-1 shadow-md">
                                    </div>
                                @endif

                                <a href="{{ asset('storage/' . ($finance->receipt_file ?? $finance->payment?->receipt_file)) }}"
                                    target="_blank"
                                    class="btn-secondary btn-sm mt-2 inline-flex">
                                        <svg class="h-4 w-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Buka File
                                </a>
                            @else
                                <p class="mt-1 text-sm italic text-stone-400">Tidak ada bukti transaksi yang diunggah.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-stone-100 pt-5">
                        <a href="{{ route('finances.index') }}" class="btn-secondary">
                            Kembali
                        </a>
                        <a href="{{ route('finances.edit', $finance) }}" class="btn-primary">
                            Edit Transaksi
                        </a>
                        <form onsubmit="confirmDelete(event, 'delete-finance-{{ $finance->id }}', 'Transaksi ini')"
                                id="delete-finance-{{ $finance->id }}"
                                action="{{ route('finances.destroy', $finance) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger">
                                Hapus
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
