<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Tambah Transaksi Keuangan') }}
        </h2>
    </x-slot>

    <div class="page-container pt-4 sm:pt-5 pb-8 sm:pb-10" x-data="{ transactionType: '{{ old('type', 'income') }}' }">
        <div class="mx-auto max-w-3xl">
            <div class="card animate-fade-in-up">
                <div class="card-body">
                    <form action="{{ route('finances.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            {{-- SEPARATOR 1: TIPE TRANSAKSI --}}
                            <div class="md:col-span-2">
                                <h3 class="section-title border-b border-stone-100 pb-2">Tipe & Kategori</h3>
                            </div>

                            <div class="md:col-span-2">
                                <label class="form-label mb-2">Tipe Transaksi</label>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <label class="relative flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 p-4 transition"
                                            :class="transactionType === 'income' ? 'border-emerald-500 bg-emerald-50' : 'border-stone-200 hover:border-emerald-400'">
                                        <input type="radio" name="type" value="income"
                                                class="sr-only"
                                                x-model="transactionType"
                                                {{ old('type', 'income') == 'income' ? 'checked' : '' }}>
                                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span class="font-semibold text-emerald-600">Pemasukan</span>
                                    </label>
                                    <label class="relative flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 p-4 transition"
                                            :class="transactionType === 'expense' ? 'border-red-500 bg-red-50' : 'border-stone-200 hover:border-red-400'">
                                        <input type="radio" name="type" value="expense"
                                                class="sr-only"
                                                x-model="transactionType"
                                                {{ old('type') == 'expense' ? 'checked' : '' }}>
                                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                        <span class="font-semibold text-red-600">Pengeluaran</span>
                                    </label>
                                </div>
                                @error('type')
                                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <div class="form-group">
                                    <label class="form-label" for="category">Kategori</label>
                                    <select name="category" id="category" required
                                            class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                        <option value="">Pilih Kategori</option>

                                        <optgroup label="Pemasukan" x-show="transactionType === 'income'">
                                            @foreach($incomeCategories as $cat)
                                            <option value="{{ $cat }}"
                                                    x-bind:selected="transactionType === 'income' && '{{ old('category') }}' === '{{ $cat }}'"
                                                    x-show="transactionType === 'income'">
                                                {{ $cat }}
                                            </option>
                                            @endforeach
                                        </optgroup>

                                        <optgroup label="Pengeluaran" x-show="transactionType === 'expense'">
                                            @foreach($expenseCategories as $cat)
                                            <option value="{{ $cat }}"
                                                    x-bind:selected="transactionType === 'expense' && '{{ old('category') }}' === '{{ $cat }}'"
                                                    x-show="transactionType === 'expense'">
                                                {{ $cat }}
                                            </option>
                                            @endforeach
                                        </optgroup>

                                    </select>
                                    @error('category')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- SEPARATOR 2: DETAIL TRANSAKSI --}}
                            <div class="mt-2 md:col-span-2">
                                <h3 class="section-title border-b border-stone-100 pb-2">Detail Transaksi</h3>
                            </div>

                            <div>
                                <div class="form-group">
                                    <label class="form-label" for="transaction_date">Tanggal Transaksi</label>
                                    <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                                        class="mt-1 block w-full cursor-pointer rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    @error('transaction_date')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div x-data="financeAmount"
                                data-amount-display="{{ number_format(old('amount', 0), 0, ',', '.') }}"
                                data-amount-clean="{{ old('amount', 0) }}">

                                <div class="form-group">
                                    <label class="form-label" for="amount_display">Jumlah (Rp)</label>

                                    <input type="text" id="amount_display"
                                        x-model="amountDisplay"
                                        x-on:input="formatNumber()"
                                        required
                                        class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                        placeholder="0">

                                    <input type="hidden" name="amount" x-model="amountClean">

                                    @error('amount')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <div class="form-group">
                                    <label class="form-label" for="description">Deskripsi</label>
                                    <input type="text" name="description" id="description" value="{{ old('description') }}" required
                                        class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                        placeholder="Contoh: Pembayaran sewa kamar 101">
                                    @error('description')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <div class="form-group">
                                    <label class="form-label" for="notes">Catatan (Optional)</label>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                        placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- SEPARATOR 3: BUKTI --}}
                            <div class="mt-2 md:col-span-2">
                                <h3 class="section-title border-b border-stone-100 pb-2">Lampiran</h3>
                            </div>

                            <div class="md:col-span-2">
                                <div class="form-group">
                                    <label class="form-label" for="receipt_file">Bukti Transaksi (Optional)</label>
                                    <input type="file" name="receipt_file" id="receipt_file" accept="image/*,.pdf"
                                        class="mt-1 block w-full text-sm text-stone-500 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                                    <p class="form-hint">PDF, JPG, JPEG, PNG (Max 2MB)</p>
                                    @error('receipt_file')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center justify-end gap-3">
                            <a href="{{ route('finances.index') }}" class="btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn-primary">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
