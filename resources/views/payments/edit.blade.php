<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <form action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">

                            {{-- Pilih Penghuni --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Penghuni</label>
                                <select name="tenant_id" id="tenant_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                    <option value="">Pilih Penghuni</option>

                                    @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}"
                                        data-price="{{ $tenant->room->price }}"
                                        {{ $payment->tenant_id == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }} - Kamar {{ $tenant->room->room_number }}
                                    </option>
                                    @endforeach

                                </select>
                                @error('tenant_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Periode & Tanggal pembayaran --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Periode Bulan</label>
                                    <input type="month" name="period_month"
                                        value="{{ old('period_month', \Carbon\Carbon::parse($payment->period_month)->format('Y-m')) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('period_month')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Pembayaran</label>
                                    <input type="date" name="payment_date"
                                        value="{{ old('payment_date', \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d')) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('payment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Amount & Late Fee --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jumlah Bayar (Rp)</label>
                                    <input type="text" name="amount" id="amount" value="{{ old('amount', $payment->amount) }}" placeholder="0" onkeyup="formatRupiah(this)" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-gray-800">                                    @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Denda Keterlambatan (Rp)</label>
                                    <input type="text" name="late_fee" id="late_fee" value="{{ old('late_fee', $payment->late_fee) }}" placeholder="0" onkeyup="formatRupiah(this)" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-gray-800">                                    @error('late_fee')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            {{-- Metode pembayaran --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                                <select name="payment_method" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                    <option value="">Pilih Metode</option>
                                    <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="transfer" {{ old('payment_method', $payment->payment_method) == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="e-wallet" {{ old('payment_method', $payment->payment_method) == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>

                                </select>
                                @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            {{-- Status Pembayaran --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                                <select name="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('status', $payment->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="overdue" {{ old('status', $payment->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                                @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Catatan (Optional)</label>
                                <textarea name="notes" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $payment->notes) }}</textarea>
                                @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Bukti pembayaran --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Bukti Pembayaran (Optional)</label>

                                @if($payment->receipt_file)
                                    <p class="text-sm text-gray-500 mb-2">File saat ini:
                                        <a href="{{ asset('storage/'.$payment->receipt_file) }}" target="_blank"
                                            class="text-blue-600 underline">Lihat / Download</a>
                                    </p>

                                    {{-- PREVIEW GAMBAR/FILE --}}
                                    @php
                                        $mime = Storage::disk('public')->mimeType($payment->receipt_file);
                                    @endphp

                                    @if (str_starts_with($mime, 'image'))
                                        <img src="{{ asset('storage/'.$payment->receipt_file) }}" 
                                            alt="Bukti Pembayaran" 
                                            class="max-w-xs h-auto border rounded-lg mb-4">
                                    @elseif ($mime == 'application/pdf')
                                        <p class="text-sm text-yellow-600 mb-2">
                                            (Tipe PDF, tidak ditampilkan *inline* di *browser*.)
                                        </p>
                                    @endif
                                    {{-- AKHIR PREVIEW --}}

                                @endif

                                <input type="file" name="receipt_file" accept="image/*,.pdf"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                                @error('receipt_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        {{-- Buttons --}}
                        <div class="mt-6 flex items-center justify-end gap-3">
                            <a href="{{ route('payments.index') }}"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</a>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan Perubahan</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- KODE JAVASCRIPT LENGKAP UNTUK FORMATTING DAN AUTOFIL --}}
    <script>
        function formatRupiah(input) {
            let value = input.value;
            
            if (value.includes('.')) {
                value = value.split('.')[0]; 
            }
            
            let number_string = value.replace(/[^0-9]/g, '').toString();
            
            if (number_string.includes('.')) {
                number_string = number_string.split('.')[0];
            }

            let sisa = number_string.length % 3,
                rupiah = number_string.substr(0, sisa),
                ribuan = number_string.substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            input.value = rupiah;
        }

        const paymentForm = document.querySelector('form');
        
        paymentForm.addEventListener('submit', function() {
            const amountInput = document.getElementById('amount');
            const lateFeeInput = document.getElementById('late_fee');
            
            amountInput.value = amountInput.value.replace(/\./g, '').replace(/,/g, '');
            lateFeeInput.value = lateFeeInput.value.replace(/\./g, '').replace(/,/g, '');
        });

        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount');
            const lateFeeInput = document.getElementById('late_fee');
            
            if (amountInput && amountInput.value) {
                formatRupiah(amountInput);
            }
            if (lateFeeInput && lateFeeInput.value) {
                formatRupiah(lateFeeInput);
            }
        });

        document.getElementById('tenant_id').addEventListener('change', function() {
            const amountInput = document.getElementById('amount');
            const selectedOption = this.options[this.selectedIndex];
            let price = selectedOption.getAttribute('data-price');
            
            if (price) {
                if (price.includes('.')) {
                    price = price.split('.')[0]; 
                }
                
                amountInput.value = price;
                formatRupiah(amountInput);
            } else {
                amountInput.value = '';
            }
        });
    </script>

</x-app-layout>
