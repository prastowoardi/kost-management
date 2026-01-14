<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Transaksi Keuangan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg sm:rounded-lg overflow-hidden">
                <div class="p-4 sm:p-6">
                    
                    {{-- Judul Transaksi --}}
                    <div class="flex justify-between items-start border-b pb-4 mb-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ $finance->description }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                ID Transaksi: <span class="font-mono">{{ $finance->id }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                @if($finance->type == 'income') 
                                    bg-green-100 text-green-800
                                @else 
                                    bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($finance->type) == 'Income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                        </div>
                    </div>

                    {{-- Detail Transaksi --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 text-gray-700">
                        
                        {{-- Baris 1: Jumlah dan Kategori --}}
                        <div>
                            <p class="text-sm font-medium text-gray-500">Jumlah Transaksi</p>
                            <p class="text-xl font-extrabold 
                                @if($finance->type == 'income') text-green-600 @else text-red-600 @endif">
                                Rp {{ number_format($finance->amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Kategori</p>
                            <p class="text-lg font-semibold">{{ $finance->category }}</p>
                        </div>

                        {{-- Baris 2: Tanggal dan Dibuat Pada --}}
                        <div class="mt-2">
                            <p class="text-sm font-medium text-gray-500">Tanggal Transaksi</p>
                            <p class="text-base">{{ \Carbon\Carbon::parse($finance->transaction_date)->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="mt-2">
                            <p class="text-sm font-medium text-gray-500">Dicatat Pada</p>
                            <p class="text-base">{{ $finance->created_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>

                        {{-- Baris 3: Catatan --}}
                        <div class="md:col-span-2 mt-4">
                            <p class="text-sm font-medium text-gray-500">Catatan</p>
                            <div class="p-3 bg-gray-50 rounded-md border border-gray-200 min-h-[50px]">
                                <p class="text-base whitespace-pre-wrap">{{ $finance->notes ?? 'Tidak ada catatan.' }}</p>
                            </div>
                        </div>

                        {{-- Baris 4: Bukti Transaksi --}}
                        <div class="md:col-span-2 mt-4">
                            <p class="text-sm font-medium text-gray-500">Bukti Transaksi</p>
                            @if($finance->receipt_file)
                                {{-- CEK JIKA FILE ADALAH GAMBAR --}}
                                @if(in_array(pathinfo($finance->receipt_file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                    <div class="mt-2 mb-3">
                                        <img src="{{ asset('storage/' . $finance->receipt_file) }}" 
                                            alt="Bukti Transaksi" 
                                            class="max-w-xs rounded-lg shadow-md border p-1">
                                    </div>
                                @endif

                                <a href="{{ asset('storage/' . $finance->receipt_file) }}" 
                                    target="_blank"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                    Lihat Full Bukti ({{ strtoupper(pathinfo($finance->receipt_file, PATHINFO_EXTENSION)) }})
                                </a>
                            @else
                                <p class="mt-2 text-base text-gray-500 italic">Tidak ada bukti transaksi terlampir.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-6 pt-4 border-t flex justify-end space-x-3">
                        <a href="{{ route('finances.index') }}" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Kembali
                        </a>
                        <a href="{{ route('finances.edit', $finance) }}" 
                            class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                            Edit Transaksi
                        </a>
                        <form onsubmit="confirmDelete('delete-finance-{{ $finance->id }}', 'Transaksi ini')" 
                                id="delete-finance-{{ $finance->id }}" 
                                action="{{ route('finances.destroy', $finance) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700">
                                Hapus
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>