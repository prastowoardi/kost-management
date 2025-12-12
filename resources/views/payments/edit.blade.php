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
                                        value="{{ old('period_month', $payment->period_month) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('period_month')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Pembayaran</label>
                                    <input type="date" name="payment_date"
                                        value="{{ old('payment_date', $payment->payment_date) }}" required
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
                                    <input type="number" id="amount" name="amount"
                                        value="{{ old('amount', $payment->amount) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Denda Keterlambatan (Rp)</label>
                                    <input type="number" name="late_fee"
                                        value="{{ old('late_fee', $payment->late_fee) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('late_fee')
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
                                    <option value="cash" {{ $payment->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="transfer" {{ $payment->payment_method == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="e-wallet" {{ $payment->payment_method == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>

                                </select>
                                @error('payment_method')
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
                                    <p class="text-sm text-gray-500 mb-1">File saat ini:
                                        <a href="{{ asset('storage/'.$payment->receipt_file) }}" target="_blank"
                                           class="text-blue-600 underline">Lihat / Download</a>
                                    </p>
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

    {{-- Auto-fill amount when tenant is changed --}}
    <script>
        document.getElementById('tenant_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                document.getElementById('amount').value = price;
            }
        });
    </script>

</x-app-layout>
