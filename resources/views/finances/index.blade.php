<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pencatatan Keuangan') }}
            </h2>
            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                <a href="{{ route('finances.dashboard') }}"
                    class="flex-1 md:flex-none justify-center inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm rounded-md hover:bg-purple-700">
                    📊 <span class="ml-1 hidden sm:inline">Dashboard</span>
                </a>
                <a href="{{ route('finances.report') }}"
                    class="flex-1 md:flex-none justify-center inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                    📄 <span class="ml-1 hidden sm:inline">Laporan</span>
                </a>
                <a href="{{ route('finances.create') }}"
                    class="flex-1 md:flex-none justify-center inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 font-bold">
                    + <span class="ml-1">Tambah</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ openDetail: false, selectedReceipt: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-gray-500 text-sm">Total Pemasukan</p>
                                <p class="text-2xl font-bold text-green-600">Rp
                                    {{ number_format($totalIncome, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 12H4" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-gray-500 text-sm">Total Pengeluaran</p>
                                <p class="text-2xl font-bold text-red-600">Rp
                                    {{ number_format($totalExpense, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div
                                class="flex-shrink-0 {{ $balance >= 0 ? 'bg-blue-500' : 'bg-orange-500' }} rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-gray-500 text-sm">Saldo</p>
                                <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                                    Rp {{ number_format($balance, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="space-y-4">
                        <div x-data="dateRangePicker()" class="border-t pt-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Rentang Tanggal</p>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Dari</label>
                                    <div class="relative">
                                        <input type="date" name="start_date" value="{{ $startDate }}"
                                            @change="endDate = null" x-model="startDate"
                                            class="w-full px-3 py-2 pl-9 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm">
                                        <svg class="absolute left-2.5 top-2.5 w-4 h-4 text-gray-400 pointer-events-none"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <template x-if="startDate">
                                        <p class="text-xs text-green-600 mt-1" x-text="formatDate(startDate)"></p>
                                    </template>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Sampai</label>
                                    <div class="relative">
                                        <input type="date" name="end_date" value="{{ $endDate }}"
                                            x-model="endDate" :min="startDate" :disabled="!startDate"
                                            :class="!startDate && 'opacity-50 cursor-not-allowed'"
                                            class="w-full px-3 py-2 pl-9 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm">
                                        <svg class="absolute left-2.5 top-2.5 w-4 h-4 text-gray-400 pointer-events-none"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <template x-if="endDate && startDate">
                                        <p class="text-xs text-green-600 mt-1" x-text="getDaysCount() + ' hari'"></p>
                                    </template>
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Quick Date
                                        Range</label>
                                    <div class="flex gap-2 flex-wrap">
                                        <button @click.prevent="setRange(0, 6)" type="button"
                                            class="px-2 py-1.5 text-xs bg-white border border-gray-300 rounded hover:bg-blue-50 whitespace-nowrap">7
                                            hari</button>
                                        <button @click.prevent="setRange(0, 29)" type="button"
                                            class="px-2 py-1.5 text-xs bg-white border border-gray-300 rounded hover:bg-blue-50 whitespace-nowrap">30
                                            hari</button>
                                        <button @click.prevent="setRangeMonth()" type="button"
                                            class="px-2 py-1.5 text-xs bg-white border border-gray-300 rounded hover:bg-blue-50 whitespace-nowrap">Bulan
                                            ini</button>
                                        <button @click.prevent="setRangeMonth(-1)" type="button"
                                            class="px-2 py-1.5 text-xs bg-white border border-gray-300 rounded hover:bg-blue-50 whitespace-nowrap">Bulan
                                            lalu</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                                <select name="type"
                                    class="w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">Semua</option>
                                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>
                                        Pemasukan</option>
                                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>
                                        Pengeluaran</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <input type="text" name="category" value="{{ request('category') }}"
                                    class="w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Nama kategori">
                            </div>

                            <div class="flex items-end">
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Deskripsi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($finances as $finance)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $finance->transaction_date->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full {{ $finance->type == 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $finance->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $finance->category }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ Str::limit($finance->description, 50) }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $finance->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $finance->type == 'income' ? '+' : '-' }} Rp
                                            {{ number_format($finance->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex items-center justify-center space-x-2">

                                                @if ($finance->type == 'income' && $finance->payment && $finance->payment->receipt_file)
                                                    <button type="button"
                                                        @click="
                                                            selectedReceipt = {
                                                                invoice_number: '{{ $finance->payment->invoice_number ?? 'INV-' . date('Ymd') . '-' . $finance->id }}',
                                                                file_url: '{{ asset('storage/' . $finance->payment->receipt_file) }}'
                                                            };
                                                            openDetail = true;
                                                        "
                                                        class="text-teal-600 hover:text-teal-900 p-1 hover:bg-teal-50 rounded transition"
                                                        title="Lihat Berkas Kwitansi">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </button>
                                                @endif

                                                <a href="{{ route('finances.show', $finance) }}"
                                                    class="text-blue-600 hover:text-blue-900 p-1 hover:bg-blue-50 rounded transition"
                                                    title="Detail Transaksi">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>

                                                <a href="{{ route('finances.edit', $finance) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 p-1 hover:bg-indigo-50 rounded transition"
                                                    title="Edit Transaksi">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>

                                                <form id="delete-finance-{{ $finance->id }}"
                                                    action="{{ route('finances.destroy', $finance) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <button
                                                    onclick="confirmDelete(event, 'delete-finance-{{ $finance->id }}', 'Transaksi ini')"
                                                    class="text-red-600 hover:text-red-900 p-1 hover:bg-red-50 rounded transition"
                                                    title="Hapus Transaksi">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada
                                            transaksi keuangan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $finances->links() }}
                    </div>
                </div>
            </div>

            <div class="fixed inset-0 z-50 overflow-y-auto" x-show="openDetail"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                    @click="openDetail = false"></div>
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-100"
                        @click.away="openDetail = false">
                        <div class="bg-slate-50 px-6 py-4 flex justify-between items-center border-b border-slate-100">
                            <div>
                                <span
                                    class="text-[10px] bg-teal-100 text-teal-800 font-black px-2 py-0.5 rounded-md uppercase tracking-wider">Serrata
                                    Kost</span>
                                <h3 class="text-sm font-black text-slate-800 mt-0.5 flex items-center gap-2">
                                    <span>Detail Kwitansi</span>
                                    <span
                                        class="font-mono bg-slate-200/60 px-2 py-0.5 rounded-md text-slate-600 text-xs tracking-wider border border-slate-300/60 shadow-inner"
                                        x-text="selectedReceipt.invoice_number"></span>
                                </h3>
                            </div>
                            <button type="button" @click="openDetail = false"
                                class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
                        </div>
                        <div class="bg-white p-6">
                            <div
                                class="w-full rounded-2xl bg-slate-50 border border-dashed border-slate-300 p-2 overflow-hidden shadow-inner">
                                <div
                                    class="w-full h-96 flex items-center justify-center overflow-auto bg-white rounded-xl">
                                    <img :src="selectedReceipt.file_url"
                                        class="max-w-full max-h-full object-contain mx-auto" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-6 py-4 flex justify-between items-center border-t border-slate-100">
                            <a :href="selectedReceipt.file_url" download
                                class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow transition flex items-center gap-1.5">
                                Download Kwitansi
                            </a>
                            <button type="button" @click="openDetail = false"
                                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script>
    function dateRangePicker() {
        return {
            startDate: '{{ $startDate }}',
            endDate: '{{ $endDate }}',

            formatDate(date) {
                return new Date(date).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
            },

            getDaysCount() {
                if (!this.startDate || !this.endDate) return 0;
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                return Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
            },

            setRange(daysStart, daysEnd) {
                const today = new Date();
                const start = new Date(today);
                start.setDate(today.getDate() - daysEnd);
                this.startDate = start.toISOString().split('T')[0];
                this.endDate = today.toISOString().split('T')[0];
            },

            setRangeMonth(offset = 0) {
                const today = new Date();
                const year = today.getFullYear();
                const month = today.getMonth() + offset;

                this.startDate = new Date(year, month, 1).toISOString().split('T')[0];
                this.endDate = new Date(year, month + 1, 0).toISOString().split('T')[0];
            }
        }
    }
</script>
