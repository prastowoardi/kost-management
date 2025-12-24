<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pembayaran') }}
            </h2>
            <div class="flex space-x-3"> 
                <a href="{{ route('payments.index') }}"
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
                        $currentMonth = request('filter_month', date('n'));
                        $currentYear = request('filter_year', date('Y'));
                        
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        $startYear = date('Y') - 3;
                        $endYear = date('Y') + 1;
                    @endphp

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
                    
                    <div>
                        <label for="invoice_number" class="block text-sm font-medium text-gray-700">Nomor Invoice</label>
                        <input type="text" name="invoice_number" id="invoice_number"
                                value="{{ request('invoice_number') }}"
                                placeholder="Cari No. Invoice..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="flex space-x-2 lg:col-span-1">
                        <button type="submit"
                                class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">
                            Filter
                        </button>
                        <a href="{{ route('payments.index') }}"
                            class="w-full px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-400 text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-4 sm:p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-max w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Penghuni</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Kamar</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bayar</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($payments as $payment)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">
                                        {{ $payment->invoice_number }}
                                        <div class="text-[10px] text-gray-400 sm:hidden">
                                            {{ $payment->tenant->name }} (Rm {{ $payment->room->room_number }})
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap hidden sm:table-cell">
                                        {{ $payment->tenant->name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap hidden md:table-cell">
                                        {{ $payment->room->room_number }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $payment->payment_date->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap">
                                        Rp {{ number_format($payment->total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 inline-flex text-[10px] leading-5 font-semibold rounded-full
                                            @if($payment->status == 'paid') bg-green-100 text-green-800
                                            @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <div class="flex items-center justify-center space-x-2">
                                            {{-- FITUR WHATSAPP --}}
                                            @if($payment->tenant->phone)
                                                <button type="button" 
                                                        onclick="sendWhatsApp('{{ $payment->id }}', '{{ $payment->tenant->name }}')"
                                                        class="p-1.5 bg-green-500 text-white rounded-md hover:bg-green-600 shadow-sm">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                </button>
                                            @endif
                                            <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900" title="Detail">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            
                                            <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="text-green-600 hover:text-green-900" title="Kwitansi">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            </a>

                                            <a href="{{ route('payments.edit', $payment) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>

                                            <form id="delete-payment-{{ $payment->id }}" action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete(event, 'delete-payment-{{ $payment->id }}', '{{ $payment->invoice_number }}')" class="text-red-600 hover:text-red-900" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Data tidak ditemukan untuk filter ini.
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
        window.confirmDelete = function(event, formId, itemName) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Data ${itemName} akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Warna merah hapus
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        function sendWhatsApp(paymentId, tenantName) {
            Swal.fire({
                title: 'Kirim Kwitansi?',
                text: `Kirim kwitansi otomatis ke WhatsApp ${tenantName}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`/payments/${paymentId}/send-wa`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal mengirim pesan');
                        return response.json();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Terkirim!',
                        text: 'Kwitansi telah dikirim oleh sistem.',
                        icon: 'success'
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if(session('new_payment_id'))
                @php
                    $newP = \App\Models\Payment::with('tenant')->find(session('new_payment_id'));
                @endphp
                @if($newP)
                    // Langsung panggil fungsi gateway
                    sendWhatsApp('{{ $newP->id }}', '{{ $newP->tenant->name }}');
                @endif
            @endif
        });
    </script>
</x-app-layout>