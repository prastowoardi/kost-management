<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('History Manual Kwitansi') }}
            </h2>
            <a href="{{ route('admin.receipt.create') }}" target="_blank" class="px-4 py-2 bg-teal-600 text-white font-bold text-sm rounded-xl shadow-md hover:bg-teal-700 transition">
                + Buat Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ openDetail: false, selectedReceipt: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-700 px-4 py-3 rounded-2xl" role="alert">
                    <strong class="font-bold">Sukses!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <form method="GET" action="{{ route('admin.receipt.history') }}" class="mb-6">
                <x-filter-panel reset="{{ route('admin.receipt.history') }}">
                    <x-filter-input name="search" label="Cari" placeholder="Nama penyewa atau invoice..." />
                    <x-filter-date name="period" label="Periode" />
                    <x-filter-input name="amount_min" label="Nominal Min" placeholder="Rp 0" />
                    <x-filter-input name="amount_max" label="Nominal Max" placeholder="Rp 9.999.999" />
                </x-filter-panel>
            </form>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-[2rem] border border-teal-50">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-500">
                            <thead class="text-xs text-slate-700 uppercase bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4">Invoice No</th>
                                    <th class="px-6 py-4">Nama Penyewa</th>
                                    <th class="px-6 py-4">Kamar</th>
                                    <th class="px-6 py-4">Periode</th>
                                    <th class="px-6 py-4">Total Diterima</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receipts as $receipt)
                                    <tr class="bg-white border-b border-slate-100 hover:bg-slate-50/80 transition">
                                        <td class="px-6 py-4 font-bold text-slate-800">{{ $receipt->invoice_number ?? '-' }}</td>
                                        <td class="px-6 py-4 font-medium text-slate-700">{{ $receipt->tenant_name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-slate-600">Kamar {{ $receipt->room_number ?? '-' }}</td>
                                        <td class="px-6 py-4 text-slate-600">
                                            {{ $receipt->period ? \Carbon\Carbon::parse($receipt->period)->translatedFormat('F Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 font-bold text-teal-600">
                                            Rp {{ number_format($receipt->total_amount ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button type="button" 
                                                @click="
                                                    selectedReceipt = {
                                                        invoice_number: '{{ $receipt->invoice_number }}',
                                                        tenant_name: '{{ $receipt->tenant_name }}',
                                                        room_number: '{{ $receipt->room_number }}',
                                                        period: '{{ $receipt->period ? \Carbon\Carbon::parse($receipt->period)->translatedFormat('F Y') : '-' }}',
                                                        total_amount: 'Rp {{ number_format($receipt->total_amount ?? 0, 0, ',', '.') }}',
                                                        render_url: '{{ route('admin.receipt.print', $receipt->id) }}?hide_buttons=1'
                                                    };
                                                    openDetail = true;
                                                "
                                                class="px-4 py-2 bg-slate-100 text-slate-700 hover:bg-teal-50 hover:text-teal-700 font-bold text-xs rounded-xl transition duration-200">
                                                Lihat Kwitansi 📄
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                            Belum ada riwayat kwitansi Serrata Kost yang dibuat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $receipts->links() }}
                    </div>
                </div>
            </div>

            <div class="fixed inset-0 z-50 overflow-y-auto" x-show="openDetail" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="openDetail = false"></div>

                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-teal-50" @click.away="openDetail = false">
                        
                        <div class="bg-slate-50 px-6 py-4 flex justify-between items-center border-b border-slate-100">
                            <div>
                                <span class="text-[10px] bg-teal-100 text-teal-800 font-black px-2 py-0.5 rounded-md uppercase tracking-wider">Serrata Kost</span>
                                <h3 class="text-sm font-black text-slate-800 mt-0.5 flex items-center gap-2">
                                    <span>Detail</span>
                                    <span class="font-mono bg-slate-100 px-2 py-0.5 rounded-md text-slate-500 text-xs tracking-wider border border-slate-200 shadow-inner" 
                                        x-text="selectedReceipt.invoice_number">
                                    </span>
                                </h3>                            
                            </div>
                            <button type="button" @click="openDetail = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
                        </div>

                        <div class="bg-white p-6 space-y-6">
                            
                            <div class="w-full rounded-2xl bg-slate-50 border border-dashed border-slate-300 p-2 overflow-hidden shadow-inner">
                                <iframe :src="selectedReceipt.render_url" id="receiptIframe" class="w-full h-96 rounded-xl bg-white border-none" scrolling="yes"></iframe>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-xs bg-slate-50 p-4 rounded-2xl">
                                <div>
                                    <span class="block font-bold text-slate-400 uppercase">Penyewa</span>
                                    <span class="text-sm font-black text-slate-800 mt-0.5" x-text="selectedReceipt.tenant_name"></span>
                                </div>
                                <div>
                                    <span class="block font-bold text-slate-400 uppercase">No. Kamar</span>
                                    <span class="text-sm font-black text-slate-800 mt-0.5" x-text="'Kamar ' + selectedReceipt.room_number"></span>
                                </div>
                                <div class="mt-2">
                                    <span class="block font-bold text-slate-400 uppercase">Periode Sewa</span>
                                    <span class="text-sm font-medium text-slate-700 mt-0.5" x-text="selectedReceipt.period"></span>
                                </div>
                                <div class="mt-2">
                                    <span class="block font-bold text-slate-400 uppercase">Total Dibayar</span>
                                    <span class="text-sm font-black text-teal-600 mt-0.5" x-text="selectedReceipt.total_amount"></span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex justify-between items-center border-t border-slate-100">
                            <button type="button" 
                                @click="document.getElementById('receiptIframe').contentWindow.postMessage('trigger-download-image', '*')"
                                class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow transition flex items-center gap-1.5">
                                Download Kwitansi
                            </button>
                            <button type="button" @click="openDetail = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition">
                                Tutup
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>