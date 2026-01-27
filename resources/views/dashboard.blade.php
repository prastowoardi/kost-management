<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    {{-- Alert Notifikasi --}}
    @if(session('success') || session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="{{ session('success') ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700' }} border-l-4 p-4 rounded shadow-sm">
                <p class="text-sm font-bold">{{ session('success') ?? session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- SECTION JATUH TEMPO --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <span class="bg-orange-500 p-1.5 rounded-lg mr-3 shadow-md shadow-orange-200">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Pengingat Jatuh Tempo
                    </h3>
                </div>

                @if($duePayments->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($duePayments as $tenant)
                            <div class="flex h-full">
                                <div class="bg-white border-t-4 border-orange-500 rounded-xl shadow-md overflow-hidden flex flex-col w-full">
                                    <div class="p-5 flex flex-col h-full">
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="pr-2">
                                                <h4 class="text-base font-bold text-gray-900 uppercase line-clamp-2">{{ $tenant->name }}</h4>
                                                <p class="text-sm text-gray-500 mt-1">Kamar {{ $tenant->room->room_number }}</p>
                                            </div>

                                            {{-- BADGE SUDAH DIPERBAIKI --}}
                                            <span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                                                {{ $tenant->days_left <= 0 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                                @if($tenant->days_left <= 0)
                                                    Hari Ini
                                                @elseif($tenant->days_left == 1)
                                                    Besok
                                                @else
                                                    H-{{ $tenant->days_left }}
                                                @endif
                                            </span>
                                        </div>
                                        
                                        <div class="mt-auto p-3 bg-gray-50 rounded-lg">
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-500">Sewa Bulanan:</span>
                                                <span class="font-bold text-gray-800">Rp {{ number_format($tenant->room->price, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between text-xs">
                                                <span class="text-gray-500">Jatuh Tempo:</span>
                                                <span class="text-gray-700 font-medium">{{ $tenant->calculated_due_date->format('d M Y') }}</span>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <form action="{{ route('send.reminder') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="tenant_id" value="{{ $tenant->id }}">
                                                <input type="hidden" name="due_date" value="{{ $tenant->calculated_due_date->format('d M Y') }}">
                                                
                                                <button type="button" 
                                                    data-id="{{ $tenant->id }}" 
                                                    data-name="{{ $tenant->name }}"
                                                    data-due="{{ $tenant->calculated_due_date->format('d M Y') }}"
                                                    class="send-wa-btn w-full flex items-center justify-center px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                        Kirim Reminder
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow p-10 text-center border-2 border-dashed border-gray-200">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900">Belum Ada Tagihan</h4>
                        <p class="text-gray-500">Tidak ada penghuni aktif yang masuk masa jatuh tempo dalam 7 hari ke depan.</p>
                    </div>
                @endif
            </div>

            {{-- RINGKASAN STATISTIK --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                    <div class="p-3 bg-blue-500 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Kamar Terisi</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $occupiedRooms }}/{{ $totalRooms }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                    <div class="p-3 bg-purple-500 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Tenant Aktif</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $activeTenants }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                    <div class="p-3 bg-yellow-500 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Lunas Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($paymentsThisMonth, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                    <div class="p-3 bg-red-500 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Tagihan Pending</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $pendingPayments }}</p>
                    </div>
                </div>
            </div>

            {{-- TABEL AKTIVITAS TERAKHIR --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Pembayaran Terbaru --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Riwayat Pembayaran Terbaru</h3>
                        <a href="{{ route('payments.index') }}" class="text-xs text-blue-600 font-bold hover:underline">Lihat Semua</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentPayments as $payment)
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold mr-3 text-xs">
                                    {{ substr($payment->tenant->name ?? '?', 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $payment->tenant->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">Kamar {{ $payment->room->room_number ?? '-' }} • {{ $payment->created_at->format('d M') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-800">Rp {{ number_format($payment->total, 0, ',', '.') }}</p>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase {{ $payment->status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $payment->status }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <p class="p-8 text-center text-gray-500 text-sm">Belum ada transaksi</p>
                        @endforelse
                    </div>
                </div>

                {{-- Keluhan Terbaru --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Keluhan Terbaru</h3>
                        <a href="{{ route('complaints.index') }}" class="text-xs text-blue-600 font-bold hover:underline">Lihat Semua</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentComplaints as $complaint)
                        <div class="p-4 hover:bg-gray-50 transition">
                            <div class="flex justify-between mb-1">
                                <p class="text-sm font-bold text-gray-800">{{ $complaint->title }}</p>
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded {{ $complaint->status == 'open' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }}">
                                    {{ $complaint->status }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $complaint->tenant->name ?? 'N/A' }} • Kamar {{ $complaint->room->room_number ?? '-' }}</p>
                        </div>
                        @empty
                        <p class="p-8 text-center text-gray-500 text-sm">Belum ada keluhan</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script>
document.querySelectorAll('.send-wa-btn').forEach(button => {
    button.addEventListener('click', function() {
        const tenantId = this.getAttribute('data-id');
        const tenantName = this.getAttribute('data-name');
        const dueDate = this.getAttribute('data-due');

        Swal.fire({
            title: 'Kirim Tagihan?',
            text: `Kirim pesan WhatsApp otomatis ke ${tenantName}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981', // green-500
            cancelButtonColor: '#6b7280', // gray-500
            confirmButtonText: 'Ya, Kirim Sekarang!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan Loading
                Swal.fire({
                    title: 'Sedang Mengirim...',
                    text: 'Harap tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });

                // Proses AJAX ke Laravel
                fetch("{{ route('send.reminder') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        tenant_id: tenantId,
                        due_date: dueDate
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Berhasil!', data.message, 'success');
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Tidak dapat terhubung ke server/gateway.', 'error');
                });
            }
        });
    });
});
</script>