<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Transaksi Keuangan') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ transactionType: '{{ old('type', $finance->type) }}' }"> 
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('finances.update', $finance) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 gap-6">
                            
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
                                                {{ old('type', $finance->type) == 'income' ? 'checked' : '' }}>
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
                                                {{ old('type', $finance->type) == 'expense' ? 'checked' : '' }}>
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
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="col-span-1 mt-4">
                                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Detail Transaksi</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Transaksi</label>
                                <input type="date" name="transaction_date" value="{{ old('transaction_date', $finance->transaction_date->format('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('transaction_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-data="{
                                amountClean: '{{ old('amount', (int)$finance->amount) }}', 
                                amountDisplay: '',
                                formatNumber() {
                                    let rawValue = this.amountDisplay.replace(/[^0-9]/g, '');
                                    this.amountClean = rawValue;
                                    if (rawValue !== '') {
                                        this.amountDisplay = Number(rawValue).toLocaleString('id-ID');
                                    } else {
                                        this.amountDisplay = '';
                                    }
                                },
                                initDisplay() {
                                    if (this.amountClean) {
                                        this.amountDisplay = Number(this.amountClean).toLocaleString('id-ID');
                                    }
                                }
                            }" x-init="initDisplay()"> 
                                <label class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                                <input type="text" x-model="amountDisplay" x-on:input="formatNumber()" required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <input type="hidden" name="amount" x-model="amountClean">
                                @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <input type="text" name="description" value="{{ old('description', $finance->description) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Catatan (Optional)</label>
                                <textarea name="notes" rows="3" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $finance->notes) }}</textarea>
                                @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- SEPARATOR 3: BUKTI --}}
                            <div class="col-span-1 mt-4">
                                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Lampiran</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Transaksi</label>
                                @if($finance->receipt_file)
                                <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                    <p class="text-xs text-gray-500 mb-2 uppercase font-bold tracking-wider">File Saat Ini:</p>
                                    @if(Str::endsWith($finance->receipt_file, '.pdf'))
                                        <a href="{{ asset('storage/' . $finance->receipt_file) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                            <span class="mr-2 text-xl">📄</span> Lihat PDF
                                        </a>
                                    @else
                                        <img src="{{ asset('storage/' . $finance->receipt_file) }}" alt="Bukti" class="max-w-xs rounded shadow-sm border">
                                    @endif
                                </div>
                                @endif
                                <input type="file" name="receipt_file" accept="image/*,.pdf"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-sm text-gray-500 italic">Upload file baru untuk mengganti lampiran lama</p>
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
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>