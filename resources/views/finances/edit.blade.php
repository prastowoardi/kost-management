<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Edit Transaksi Keuangan') }}
        </h2>
    </x-slot>

    <div class="page-container pt-4 sm:pt-5 pb-8 sm:pb-10" x-data="{ transactionType: '{{ old('type', $finance->type) }}' }">
        <div class="mx-auto max-w-3xl">
            <div class="card animate-fade-in-up">
                <div class="card-body">
                    <form action="{{ route('finances.update', $finance) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

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
                                                {{ old('type', $finance->type) == 'income' ? 'checked' : '' }}>
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
                                                {{ old('type', $finance->type) == 'expense' ? 'checked' : '' }}>
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
                                                    x-show="transactionType === 'income'"
                                                    {{ old('category', $finance->category) == $cat ? 'selected' : '' }}>
                                                {{ $cat }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Pengeluaran" x-show="transactionType === 'expense'">
                                            @foreach($expenseCategories as $cat)
                                            <option value="{{ $cat }}"
                                                    x-show="transactionType === 'expense'"
                                                    {{ old('category', $finance->category) == $cat ? 'selected' : '' }}>
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

                            <div class="mt-2 md:col-span-2">
                                <h3 class="section-title border-b border-stone-100 pb-2">Detail Transaksi</h3>
                            </div>

                            <div>
                                <div class="form-group">
                                    <label class="form-label" for="transaction_date">Tanggal Transaksi</label>
                                    <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', $finance->transaction_date->format('Y-m-d')) }}" required
                                        class="mt-1 block w-full cursor-pointer rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    @error('transaction_date')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div x-data="financeAmount"
                                data-amount-clean="{{ old('amount', (int) $finance->amount) }}">
                                <div class="form-group">
                                    <label class="form-label" for="amount_display">Jumlah (Rp)</label>
                                    <input type="text" id="amount_display" x-model="amountDisplay" x-on:input="formatNumber()" required
                                        class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    <input type="hidden" name="amount" x-model="amountClean">
                                    @error('amount')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <div class="form-group">
                                    <label class="form-label" for="description">Deskripsi</label>
                                    <input type="text" name="description" id="description" value="{{ old('description', $finance->description) }}" required
                                        class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    @error('description')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <div class="form-group">
                                    <label class="form-label" for="notes">Catatan (Optional)</label>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="mt-1 block w-full rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('notes', $finance->notes) }}</textarea>
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
                                    <label class="form-label">Bukti Transaksi</label>
                                    @if($finance->receipt_file)
                                    <div class="mb-3 rounded-xl border border-dashed border-stone-300 bg-stone-50 p-3">
                                        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-stone-500">File Saat Ini:</p>
                                        @if(Str::endsWith($finance->receipt_file, '.pdf'))
                                            <a href="{{ Storage::url($finance->receipt_file) }}" target="_blank" class="inline-flex items-center font-medium text-brand-600 hover:text-brand-800">
                                                <span class="mr-2 text-xl">📄</span> Lihat PDF
                                            </a>
                                        @else
                                            <img src="{{ Storage::url($finance->receipt_file) }}" alt="Bukti" class="max-w-xs rounded-xl border border-stone-200 shadow-sm">
                                        @endif
                                    </div>
                                    @endif
                                    <input type="file" name="receipt_file" id="receipt_file" accept="image/*,.pdf"
                                        class="mt-1 block w-full text-sm text-stone-500 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                                    <p class="form-hint">Upload file baru untuk mengganti lampiran lama</p>
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
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
