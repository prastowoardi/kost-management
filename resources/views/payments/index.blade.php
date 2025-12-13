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

            {{-- FILTER FORM START --}}
            <div class="mb-6 bg-white shadow-md rounded-lg p-4">
                <form method="GET" action="{{ route('payments.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-4 items-end">
                    
                    @php
                        // Ambil filter yang sedang aktif, atau set default ke bulan/tahun saat ini
                        $currentMonth = request('filter_month', date('n'));
                        $currentYear = request('filter_year', date('Y'));
                        
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        $startYear = date('Y') - 3; // Mulai 3 tahun ke belakang
                        $endYear = date('Y') + 1;  // Sampai tahun depan
                    @endphp

                    {{-- FILTER: BULAN --}}
                    <div>
                        <label for="filter_month" class="block text-sm font-medium text-gray-700">Bulan</label>
                        <select name="filter_month" id="filter_month"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua</option>
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ (string)request('filter_month', $currentMonth) == (string)$num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FILTER: TAHUN --}}
                    <div>
                        <label for="filter_year" class="block text-sm font-medium text-gray-700">Tahun</label>
                        <select name="filter_year" id="filter_year"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua</option>
                            @for($year = $endYear; $year >= $startYear; $year--)
                                <option value="{{ $year }}" {{ (string)request('filter_year', $currentYear) == (string)$year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- FILTER: PENGHUNI --}}
                    <div>
                        <label for="tenant_id" class="block text-sm font-medium text-gray-700">Penghuni</label>
                        <select name="tenant_id" id="tenant_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Penghuni</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}" 
                                    {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                    {{ $tenant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- FILTER: INVOICE NUMBER SEARCH --}}
                    <div>
                        <label for="invoice_number" class="block text-sm font-medium text-gray-700">Nomor Invoice</label>
                        <input type="text" name="invoice_number" id="invoice_number"
                                value="{{ request('invoice_number') }}"
                                placeholder="Cari No. Invoice..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="flex space-x-2 lg:col-span-1">
                        <button type="submit"
                                class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">
                            Filter
                        </button>
                        {{-- Tombol Reset --}}
                        <a href="{{ route('payments.index') }}"
                           class="w-full px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-400 text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
            {{-- FILTER FORM END --}}


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
                                        <form id="delete-payment-{{ $payment->id }}" action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button onclick="confirmDelete(event, 'delete-payment-{{ $payment->id }}', 'Pembayaran {{ $payment->invoice_number }}')"
                                                class="text-red-600 hover:text-red-900">
                                            Hapus
                                        </button>
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
                        {{ $payments->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
    
    <script>
        window.confirmDelete = function (event, formId, itemName) {
            if (confirm(`Apakah Anda yakin ingin menghapus data ${itemName}? Data ini tidak dapat dikembalikan.`)) {
                document.getElementById(formId).submit();
            }
        }
    </script>
</x-app-layout>