<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Pembayaran') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Formulir Filter --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Filter Laporan</h3>
                    
                    <form action="{{ route('payments.report') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:space-x-4 items-end">
                        
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                value="{{ request('start_date') }}">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                value="{{ request('end_date') }}">
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Semua Status</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>

                        <div class="flex space-x-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Tampilkan
                            </button>
                            
                            {{-- Tombol Unduh PDF --}}
                            @if($payments->count() > 0)
                            <button type="submit" name="download" value="pdf" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                ⬇️ Unduh PDF
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel Laporan Hasil Filter --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Hasil Laporan ({{ $payments->count() }} Transaksi)</h3>
                    
                    @if($payments->isEmpty() && request()->all())
                        <p class="text-center text-gray-500">Tidak ada data pembayaran yang ditemukan sesuai kriteria filter.</p>
                    @elseif($payments->isEmpty())
                        <p class="text-center text-gray-500">Silakan gunakan filter di atas untuk melihat data laporan.</p>
                    @else
                        {{-- Memanggil partial untuk menampilkan tabel --}}
                        @include('payments.partials.report_table', ['payments' => $payments])
                    @endif
                    
                    {{-- Total Jumlah --}}
                    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end">
                        <p class="text-xl font-bold text-gray-800">
                            Total Pemasukan: Rp {{ number_format($totalAmount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>