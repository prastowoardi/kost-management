<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pembayaran') }}
            </h2>
            <div class="flex space-x-3"> 
                <a href="{{ route('reports.payments') }}"
                    class="inline-flex justify-center items-center px-3 py-2 sm:px-4 sm:py-2 bg-green-600 text-white text-xs sm:text-sm font-semibold tracking-widest rounded-md hover:bg-green-700">
                    📊 Laporan
                </a>
                <a href="{{ route('payments.create') }}"
                    class="inline-flex justify-center items-center px-3 py-2 sm:px-4 sm:py-2 bg-blue-600 text-white text-xs sm:text-sm font-semibold tracking-widest rounded-md hover:bg-blue-700">
                    + Tambah Pembayaran
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-4 sm:p-6">

                    <div class="overflow-x-auto">
                        <table class="min-w-max w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                                    {{-- Menggunakan hidden untuk responsif: Sembunyikan kolom yang kurang penting di layar kecil --}}
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Penghuni</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Kamar</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Periode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Bayar</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($payments as $payment)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">
                                        {{ $payment->invoice_number }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap hidden sm:table-cell">
                                        {{ $payment->tenant->name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap hidden md:table-cell">
                                        {{ $payment->room->room_number }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap hidden lg:table-cell">
                                        {{ $payment->period_month->format('M Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $payment->payment_date->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap">
                                        Rp {{ number_format($payment->total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full
                                            @if($payment->status == 'paid') bg-green-100 text-green-800
                                            @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    {{-- Kolom Aksi dibuat fleksibel untuk mobile --}}
                                    <td class="px-4 py-3 whitespace-nowrap text-sm flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-2 sm:items-center">
                                        <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900 block sm:inline">Detail</a>
                                        <a href="{{ route('payments.receipt', $payment) }}" class="text-green-600 hover:text-green-900 block sm:inline">Kwitansi</a>
                                        <a href="{{ route('payments.edit', $payment) }}" class="text-indigo-600 hover:text-indigo-900 block sm:inline">Edit</a>
                                        <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pembayaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 block sm:inline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-4 text-center text-gray-500">
                                        Belum ada data pembayaran
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>