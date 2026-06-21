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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Pencarian Gagal!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white p-6 rounded-lg shadow-sm">
                <form method="GET" action="{{ route('finances.index') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end" x-data="dateRangePicker()">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Awal (Start)</label>
                        <input type="text" id="start_date_input" name="start_date" x-model="startDate"
                            placeholder="Pilih Tanggal Awal"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm cursor-pointer bg-white"
                            readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir (End)</label>
                        <input type="text" id="end_date_input" name="end_date" x-model="endDate"
                            placeholder="Pilih Tanggal Akhir"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm cursor-pointer bg-white"
                            readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Transaksi</label>
                        <select name="type"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua</option>
                            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan
                            </option>
                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua</option>
                            @foreach ($incomeCategories->merge($expenseCategories)->unique() as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div
                        class="md:col-span-4 flex flex-wrap justify-between items-center gap-2 pt-2 border-t border-gray-100">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="setRange(7)"
                                class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md">7 Hari
                                Terakhir</button>
                            <button type="button" @click="setRange(30)"
                                class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md">30 Hari
                                Terakhir</button>
                            <button type="button" @click="setRangeMonth(0)"
                                class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md">Bulan
                                Ini</button>
                        </div>
                        <div class="flex gap-2 w-full md:w-auto">
                            <a href="{{ route('finances.index') }}"
                                class="w-full md:w-auto text-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-md">Reset</a>
                            <button type="submit"
                                class="w-full md:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">Cari</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm">
                    <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Total Pemasukan</p>
                    <p class="text-2xl font-bold text-emerald-900 mt-1">Rp
                        {{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-lg shadow-sm">
                    <p class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-rose-900 mt-1">Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm">
                    <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Sisa Saldo</p>
                    <p class="text-2xl font-bold text-blue-900 mt-1">Rp {{ number_format($balance, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Tanggal</th>
                                    <th class="px-6 py-3">Kategori</th>
                                    <th class="px-6 py-3">Keterangan</th>
                                    <th class="px-6 py-3 text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($finances as $finance)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($finance->transaction_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded {{ $finance->type == 'income' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                {{ $finance->category }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">{{ $finance->description ?? '-' }}</td>
                                        <td
                                            class="px-6 py-4 text-right font-bold {{ $finance->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $finance->type == 'income' ? '+' : '-' }} Rp
                                            {{ number_format($finance->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a href="{{ route('finances.show', $finance) }}"
                                                    class="text-blue-600 hover:text-blue-900 mr-3"
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
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
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
                                                    class="text-red-600 hover:text-red-900" title="Hapus Transaksi">
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
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-400">Tidak ada data
                                            transaksi ditemukan pada rentang tanggal ini.</td>
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

        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    function dateRangePicker() {
        return {
            startDate: '{{ $startDate }}',
            endDate: '{{ $endDate }}',
            startFp: null,
            endFp: null,

            init() {
                this.startFp = flatpickr("#start_date_input", {
                    dateFormat: "Y-m-d",
                    defaultDate: this.startDate,
                    onChange: (selectedDates, dateStr) => {
                        this.startDate = dateStr;
                        this.endFp.set('minDate', dateStr);
                    }
                });

                this.endFp = flatpickr("#end_date_input", {
                    dateFormat: "Y-m-d",
                    defaultDate: this.endDate,
                    minDate: this.startDate,
                    onChange: (selectedDates, dateStr) => {
                        this.endDate = dateStr;
                    }
                });
            },

            setRange(days) {
                const today = new Date();
                const start = new Date();
                start.setDate(today.getDate() - days);

                this.startDate = start.toISOString().split('T')[0];
                this.endDate = today.toISOString().split('T')[0];

                this.startFp.setDate(this.startDate);
                this.endFp.setDate(this.endDate);
                this.endFp.set('minDate', this.startDate);
            },

            setRangeMonth(offset = 0) {
                const today = new Date();
                const year = today.getFullYear();
                const month = today.getMonth() + offset;

                this.startDate = new Date(year, month, 1).toISOString().split('T')[0];
                this.endDate = new Date(year, month + 1, 0).toISOString().split('T')[0];

                this.startFp.setDate(this.startDate);
                this.endFp.setDate(this.endDate);
                this.endFp.set('minDate', this.startDate);
            }
        }
    }
</script>
