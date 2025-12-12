<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Penghuni</label>
                                <select name="tenant_id" id="tenant_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Penghuni</option>
                                    @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" data-price="{{ $tenant->room->price }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }} - Kamar {{ $tenant->room->room_number }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('tenant_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Periode Bulan</label>
                                    <input type="month" name="period_month" value="{{ old('period_month', date('Y-m')) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('period_month')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Pembayaran</label>
                                    <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('payment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jumlah Bayar (Rp)</label>
                                    <input type="text" name="amount" id="amount" value="{{ old('amount') }}" placeholder="0"
                                        onkeyup="formatRupiah(this)"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-gray-800">
                                    @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Denda Keterlambatan (Rp)</label>
                                    <input type="text" name="late_fee" id="late_fee" value="{{ old('late_fee') }}" placeholder="0"
                                        onkeyup="formatRupiah(this)"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-gray-800">
                                    @error('late_fee')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                                <select name="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Metode</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="e-wallet" {{ old('payment_method') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                                </select>
                                @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Catatan (Optional)</label>
                                <textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                                @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Bukti Pembayaran (Optional)</label>
                                <input type="file" name="receipt_file" accept="image/*,.pdf"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('receipt_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3">
                            <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    /**
     * Fungsi Utama: Memformat input menjadi Rupiah (pemisah ribuan: titik)
     * Fungsi ini sekarang lebih aman dengan membuang karakter non-digit dan desimal.
     */
    function formatRupiah(input) {
        // Ambil nilai input. Pertama, hapus semua karakter non-digit (0-9)
        // KECUALI jika Anda menggunakan desimal, tapi untuk Rupiah bulat kita buang semua.
        let number_string = input.value.replace(/[^0-9]/g, '').toString();

        // Jika nilai dari DB adalah 1500000.00, ini mungkin menghasilkan 150000000.
        // Solusi: Kita pastikan formatnya bersih sebelum diproses.
        
        // Cek apakah ada desimal sisa dan buang (misal dari DB: 1500000.00)
        if (number_string.includes('.')) {
            number_string = number_string.split('.')[0];
        }

        let sisa = number_string.length % 3,
            rupiah = number_string.substr(0, sisa),
            ribuan = number_string.substr(sisa).match(/\d{3}/gi);

        // Tambahkan titik sebagai pemisah
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        input.value = rupiah;
    }
    
    // Bersihkan Format Sebelum Form Disubmit
    const paymentForm = document.querySelector('form');
    paymentForm.addEventListener('submit', function() {
        const amountInput = document.getElementById('amount');
        const lateFeeInput = document.getElementById('late_fee');
        
        // Hapus semua titik/koma (separator) agar backend menerima angka murni (e.g., 1000000)
        amountInput.value = amountInput.value.replace(/\./g, '').replace(/,/g, '');
        lateFeeInput.value = lateFeeInput.value.replace(/\./g, '').replace(/,/g, '');
    });

    // Inisialisasi: Format Nilai Lama (old value) Saat Halaman Dimuat
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');
        const lateFeeInput = document.getElementById('late_fee');

        if (amountInput.value) {
            formatRupiah(amountInput);
        }
        if (lateFeeInput.value) {
            formatRupiah(lateFeeInput);
        }
    });

    // Autofill: Mengisi Jumlah Bayar Saat Penghuni Dipilih (dan Langsung Diformat)
    document.getElementById('tenant_id').addEventListener('change', function() {
        const amountInput = document.getElementById('amount');
        const selectedOption = this.options[this.selectedIndex];
        let price = selectedOption.getAttribute('data-price'); // Nilai DB: 1500000.00
        
        if (price) {
            // PERBAIKAN: Hapus bagian desimal (.00) dari harga DB
            if (price.includes('.')) {
                price = price.split('.')[0]; 
            }
            
            amountInput.value = price; // Nilai murni masuk (e.g., 1500000)
            formatRupiah(amountInput); // Tampilan diformat (e.g., 1.500.000)
        } else {
            amountInput.value = '';
        }
    });
</script>
</x-app-layout>