<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Tambah Pembayaran') }}
        </h2>
    </x-slot>

    <div class="page-container pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="mx-auto max-w-3xl">
            <div class="card animate-fade-in-up">
                <div class="card-body">
                    <form id="paymentForm" action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <div class="form-group">
                                    <label class="form-label" for="tenant_id">Penghuni</label>
                                    <select name="tenant_id" id="tenant_id" required class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                        <option value="">Pilih Penghuni</option>
                                        @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" data-price="{{ $tenant->room->price }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                            {{ $tenant->name }} - Kamar {{ $tenant->room->room_number }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('tenant_id')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <div class="form-group">
                                    <label class="form-label" for="period_month">Periode Bulan</label>
                                    <input type="month" name="period_month" id="period_month" value="{{ old('period_month', date('Y-m')) }}" required
                                        class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    @error('period_month')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <div class="form-group">
                                    <label class="form-label" for="payment_date">Tanggal Pembayaran</label>
                                    <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                                        onclick="this.showPicker()"
                                        class="mt-1 block w-full cursor-pointer rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    @error('payment_date')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <div class="form-group">
                                    <label class="form-label" for="amount">Jumlah Bayar (Rp)</label>
                                    <input type="text" name="amount" id="amount" value="{{ old('amount') }}" placeholder="0"
                                        onkeyup="formatRupiah(this)"
                                        class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-stone-800">
                                    @error('amount')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <div class="form-group">
                                    <label class="form-label" for="late_fee">Denda Keterlambatan (Rp)</label>
                                    <input type="text" name="late_fee" id="late_fee" value="{{ old('late_fee') }}" placeholder="0"
                                        onkeyup="formatRupiah(this)"
                                        class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-stone-800">
                                    @error('late_fee')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <div class="form-group">
                                    <label class="form-label" for="payment_method">Metode Pembayaran</label>
                                    <select name="payment_method" id="payment_method" required class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                        <option value="">Pilih Metode</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                        <option value="e-wallet" {{ old('payment_method') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                                    </select>
                                    @error('payment_method')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <div class="form-group">
                                    <label class="form-label" for="notes">Catatan (Optional)</label>
                                    <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('notes') }}</textarea>
                                    @error('notes')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <div class="form-group">
                                    <label class="form-label" for="receipt_file_input">Bukti Pembayaran (Optional)</label>

                                    {{-- Elemen Preview Dinamis --}}
                                    <img id="receiptPreview"
                                        src="#"
                                        alt="Preview Bukti Pembayaran"
                                        class="mt-2 hidden max-w-xs rounded-xl border border-stone-200 object-cover shadow-sm"
                                    >

                                    <input type="file" name="receipt_file" id="receipt_file_input" accept="image/*,.pdf"
                                        class="mt-2 block w-full text-sm text-stone-500 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                                    @error('receipt_file')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center justify-end gap-3">
                            <a href="{{ route('payments.index') }}" class="btn-secondary">
                                Batal
                            </a>
                            <button type="submit" id="btnSubmit" class="btn-primary">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('receipt_file_input');
        const previewImage = document.getElementById('receiptPreview');

        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];

            // Hanya tampilkan jika file adalah gambar
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.classList.remove('hidden');
                };

                reader.readAsDataURL(file);
            } else {
                // Sembunyikan jika bukan gambar (misal: PDF atau tidak ada file)
                previewImage.classList.add('hidden');
                previewImage.src = '#';
            }
        });
    })

    function formatRupiah(input) {


        let number_string = input.value.replace(/[^0-9]/g, '').toString();

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

    const paymentForm = document.getElementById('paymentForm');
    const btnSubmit = document.getElementById('btnSubmit');

    paymentForm.addEventListener('submit', function(e) {
        const amountInput = document.getElementById('amount');
        const lateFeeInput = document.getElementById('late_fee');

        if(amountInput.value) {
            amountInput.value = amountInput.value.replace(/\./g, '').replace(/,/g, '');
        }
        if(lateFeeInput.value) {
            lateFeeInput.value = lateFeeInput.value.replace(/\./g, '').replace(/,/g, '');
        }

        setTimeout(() => {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Memproses...';
            btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
        }, 10);
    });

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
