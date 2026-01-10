<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Transaksi Keuangan') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ transactionType: '{{ old('type', 'income') }}' }">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('finances.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6">

                            {{-- SEPARATOR 1: TIPE TRANSAKSI --}}
                            <div class="col-span-1">
                                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Tipe & Kategori</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Transaksi</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition"
                                            :class="transactionType === 'income' ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-green-500'">
                                        <input type="radio" name="type" value="income" 
                                                class="sr-only" 
                                                x-model="transactionType"
                                                {{ old('type', 'income') == 'income' ? 'checked' : '' }}>
                                        <div class="text-center">
                                            <div class="text-3xl mb-2">💰</div>
                                            <div class="font-semibold text-green-600">Pemasukan</div>
                                        </div>
                                    </label>
                                    <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition"
                                            :class="transactionType === 'expense' ? 'border-red-500 bg-red-50' : 'border-gray-300 hover:border-red-500'">
                                        <input type="radio" name="type" value="expense" 
                                                class="sr-only"
                                                x-model="transactionType"
                                                {{ old('type') == 'expense' ? 'checked' : '' }}>
                                        <div class="text-center">
                                            <div class="text-3xl mb-2">💸</div>
                                            <div class="font-semibold text-red-600">Pengeluaran</div>
                                        </div>
                                    </label>
                                </div>
                                @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                <select name="category" required 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- SEPARATOR 2: DETAIL TRANSAKSI --}}
                            <div class="col-span-1 mt-4">
                                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Detail Transaksi</h3>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Transaksi</label>
                                <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                                    onclick="this.showPicker()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                                @error('transaction_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-data="{
                                amountDisplay: '{{ number_format(old('amount', 0), 0, ',', '.') }}',
                                amountClean: '{{ old('amount', 0) }}',
                                formatNumber() {
                                    // 1. Bersihkan dari semua karakter non-angka (termasuk titik/koma)
                                    let rawValue = this.amountDisplay.replace(/[^0-9]/g, '');
                                    
                                    // 2. Simpan nilai murni ke hidden input
                                    this.amountClean = rawValue;

                                    // 3. Format untuk tampilan
                                    if (rawValue !== '') {
                                        this.amountDisplay = Number(rawValue).toLocaleString('id-ID', {
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        });
                                    } else {
                                        this.amountDisplay = '';
                                    }
                                }
                            }" 
                            x-init="formatNumber()">
                                
                                <label class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                                
                                {{-- INPUT VISIBLE (DIFORMAT) --}}
                                <input type="text" 
                                    x-model="amountDisplay" 
                                    x-on:input="formatNumber()"
                                    required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="0">
                                
                                {{-- INPUT HIDDEN (NILAI MURNI UNTUK SUBMIT KE BACKEND) --}}
                                <input type="hidden" name="amount" x-model="amountClean">
                                
                                @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <input type="text" name="description" value="{{ old('description') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Contoh: Pembayaran sewa kamar 101">
                                @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Catatan (Optional)</label>
                                <textarea name="notes" rows="3" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                                @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- SEPARATOR 3: BUKTI --}}
                            <div class="col-span-1 mt-4">
                                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Lampiran</h3>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Transaksi (Optional)</label>
                                <input type="file" name="receipt_file" accept="image/*,.pdf"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-sm text-gray-500">PDF, JPG, JPEG, PNG (Max 2MB)</p>
                                @error('receipt_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3">
                            <a href="{{ route('finances.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
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
</x-app-layout>