<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pembayaran') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('payments.receipt', $payment) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    📄 Download Kwitansi
                </a>
                <a href="{{ route('payments.edit', $payment) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    ✏️ Edit
                </a>
                <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Send Whatsapp --}}
            @if($payment->tenant->phone)
            <form action="{{ route('payments.sendWhatsApp', $payment) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    <span>Kirim via WhatsApp</span>
                </button>
            </form>
            @else
            <span class="text-sm text-gray-500">No. WA tidak tersedia</span>
            @endif
            
            <!-- Invoice Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-800">Invoice</h3>
                            <p class="text-gray-600 mt-1">{{ $payment->invoice_number }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full 
                                @if($payment->status == 'paid') bg-green-100 text-green-800
                                @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ strtoupper($payment->status) }}
                            </span>
                            <p class="text-sm text-gray-600 mt-2">
                                {{ $payment->payment_date->format('d F Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6 border-t pt-6">
                        <!-- Informasi Penghuni -->
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">Informasi Penghuni</h4>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    @if($payment->tenant->photo)
                                    <img src="{{ asset('storage/' . $payment->tenant->photo) }}" 
                                            class="h-16 w-16 rounded-full object-cover mr-4" 
                                            alt="{{ $payment->tenant->name }}">
                                    @else
                                    <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                                        <span class="text-gray-600 font-bold text-xl">{{ substr($payment->tenant->name, 0, 1) }}</span>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $payment->tenant->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $payment->tenant->email }}</p>
                                        <p class="text-sm text-gray-600">{{ $payment->tenant->phone }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kamar -->
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">Informasi Kamar</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nomor Kamar:</span>
                                    <span class="font-semibold text-gray-900">{{ $payment->room->room_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tipe:</span>
                                    <span class="font-semibold text-gray-900">{{ ucfirst($payment->room->type) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Harga Sewa:</span>
                                    <span class="font-semibold text-gray-900">Rp {{ number_format($payment->room->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Pembayaran -->
                    <div class="border-t pt-6">
                        <h4 class="font-semibold text-gray-800 mb-4">Detail Pembayaran</h4>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Periode:</span>
                                <span class="font-semibold text-gray-900">{{ $payment->period_month->format('F Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal Pembayaran:</span>
                                <span class="font-semibold text-gray-900">{{ $payment->payment_date->format('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Metode Pembayaran:</span>
                                <span class="font-semibold text-gray-900">{{ ucfirst(str_replace('-', ' ', $payment->payment_method ?? '-')) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Biaya -->
                    <div class="border-t pt-6 mt-6">
                        <div class="space-y-3">
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-600">Biaya Sewa:</span>
                                <span class="font-semibold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                            </div>
                            @if($payment->late_fee > 0)
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-600">Denda Keterlambatan:</span>
                                <span class="font-semibold text-red-600">Rp {{ number_format($payment->late_fee, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="border-t pt-3 flex justify-between text-2xl font-bold">
                                <span class="text-gray-800">TOTAL:</span>
                                <span class="text-blue-600">Rp {{ number_format($payment->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan -->
                    @if($payment->notes)
                    <div class="border-t pt-6 mt-6">
                        <h4 class="font-semibold text-gray-800 mb-2">Catatan:</h4>
                        <p class="text-gray-600 bg-gray-50 p-4 rounded-lg">{{ $payment->notes }}</p>
                    </div>
                    @endif

                    <!-- Bukti Pembayaran -->
                    @if($payment->receipt_file)
                    <div class="border-t pt-6 mt-6">
                        <h4 class="font-semibold text-gray-800 mb-3">Bukti Pembayaran:</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            @if(Str::endsWith($payment->receipt_file, '.pdf'))
                                <a href="{{ asset('storage/' . $payment->receipt_file) }}" 
                                    target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    📄 Lihat PDF
                                </a>
                            @else
                                <img src="{{ asset('storage/' . $payment->receipt_file) }}" 
                                        alt="Bukti Pembayaran" 
                                        class="max-w-md rounded-lg border border-gray-300">
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Timestamp -->
                    <div class="border-t pt-4 mt-6 text-sm text-gray-500">
                        <div class="flex justify-between">
                            <span>Dibuat: {{ $payment->created_at->format('d F Y H:i') }}</span>
                            <span>Diupdate: {{ $payment->updated_at->format('d F Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>