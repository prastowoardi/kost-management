<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6 border-b pb-4">
                        <h3 class="text-lg font-semibold text-blue-600">Informasi Umum</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">
                        
                        {{-- Tenant dan Kamar --}}
                        <div>
                            <p class="text-sm font-medium text-gray-500">Penghuni</p>
                            <p class="text-base font-semibold">{{ $payment->tenant->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Kamar</p>
                            <p class="text-base font-semibold">{{ $payment->room->room_number ?? 'N/A' }}</p>
                        </div>

                        {{-- Periode dan Tanggal --}}
                        <div>
                            <p class="text-sm font-medium text-gray-500">Periode Bulan</p>
                            <p class="text-base font-semibold">
                                {{ \Carbon\Carbon::parse($payment->period_month)->translatedFormat('F Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tanggal Pembayaran</p>
                            <p class="text-base font-semibold">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('d F Y') }}
                            </p>
                        </div>

                        {{-- Status dan Metode --}}
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            @php
                                $statusClass = [
                                    'paid' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'overdue' => 'bg-red-100 text-red-800',
                                ][$payment->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Metode Pembayaran</p>
                            <p class="text-base font-semibold">{{ ucfirst($payment->payment_method) }}</p>
                        </div>

                    </div>

                    <div class="my-6 border-b border-t pt-4 pb-4">
                        <h3 class="text-lg font-semibold text-blue-600">Detail Biaya</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">
                        {{-- Jumlah Bayar --}}
                        <div>
                            <p class="text-sm font-medium text-gray-500">Jumlah Bayar (Pokok)</p>
                            <p class="text-lg font-bold text-gray-800">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- Denda --}}
                        <div>
                            <p class="text-sm font-medium text-gray-500">Denda Keterlambatan</p>
                            <p class="text-lg font-bold text-red-600">
                                Rp {{ number_format($payment->late_fee, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- Total --}}
                        <div class="md:col-span-2 mt-4 pt-4 border-t">
                            <p class="text-sm font-medium text-gray-500">Total Pembayaran</p>
                            <p class="text-2xl font-extrabold text-blue-700">
                                Rp {{ number_format($payment->total, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="my-6 border-b border-t pt-4 pb-4">
                        <h3 class="text-lg font-semibold text-blue-600">Catatan & Bukti</h3>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Catatan</p>
                        <p class="text-base italic text-gray-700">{{ $payment->notes ?? 'Tidak ada catatan.' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Bukti Pembayaran</p>
                        @if ($payment->receipt_file)
                            <a href="{{ asset('storage/' . $payment->receipt_file) }}" target="_blank"
                            class="inline-flex items-center text-base text-blue-600 hover:text-blue-800 underline mt-1 mb-3">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Lihat / Unduh File Bukti
                            </a>

                            {{-- PREVIEW GAMBAR/FILE --}}
                            @php
                                // Anda perlu memastikan Storage diimport di Controller!
                                $mime = Storage::disk('public')->mimeType($payment->receipt_file);
                            @endphp

                            @if (str_starts_with($mime, 'image'))
                                <img src="{{ asset('storage/'.$payment->receipt_file) }}" 
                                    alt="Bukti Pembayaran" 
                                    class="max-w-sm h-auto border rounded-lg">
                            @elseif ($mime == 'application/pdf')
                                <p class="text-base text-yellow-600">
                                    Tipe PDF: Gunakan tautan di atas untuk melihat/mengunduh.
                                </p>
                            @endif
                            {{-- AKHIR PREVIEW --}}

                        @else
                            <p class="text-base text-gray-500 mt-1">Tidak ada bukti pembayaran diunggah.</p>
                        @endif
                    </div>
                    
                    {{-- Tombol Aksi --}}
                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('payments.index') }}"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Kembali
                        </a>
                        <a href="{{ route('payments.edit', $payment->id) }}"
                            class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                            Edit Pembayaran
                        </a>
                        {{-- Tambahkan tombol download jika Anda memiliki rute download receipt --}}
                        {{-- <a href="{{ route('payments.downloadReceipt', $payment->id) }}"
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                            Cetak Kwitansi
                        </a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>