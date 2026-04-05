<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('History Kwitansi') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.receipt.history') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- FILTER SECTION --}}
            <div class="mb-6 bg-white shadow-sm rounded-lg p-4 border border-gray-100">
                <form method="GET" action="{{ route('admin.receipt.history') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    {{-- Pencarian Nama (Input Text agar tidak butuh variabel $tenants) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Penyewa</label>
                        <input type="text" name="search_name" value="{{ request('search_name') }}" placeholder="Cari nama..." 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                    </div>

                    {{-- Pencarian Invoice --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">No. Invoice</label>
                        <input type="text" name="invoice" value="{{ request('invoice') }}" placeholder="Contoh: 001..." 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-teal-600 text-white font-semibold rounded-md hover:bg-teal-700 transition">
                            Cari Data
                        </button>
                        <a href="{{ route('admin.receipt.history') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-md hover:bg-gray-300 text-center transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- TABLE SECTION --}}
            <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Invoice</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Penyewa</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Total</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($history as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $item->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-mono bg-gray-100 text-gray-600 rounded border border-gray-200">
                                        #{{ $item->invoice_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800">
                                    {{ $item->tenant_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-teal-600">
                                    Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('admin.receipt.print', $item->id) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 bg-white border border-teal-600 text-teal-600 text-xs font-bold rounded-lg hover:bg-teal-600 hover:text-white transition-all">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        Cetak
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                    Data riwayat tidak ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Navigasi Halaman (Jika ada pagination) --}}
                @if(method_exists($history, 'links'))
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $history->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>