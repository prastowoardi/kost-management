<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    {{-- Alert Notifikasi --}}
    @if(session('success') || session('error'))
        <div class="page-container mt-4">
            <div class="alert {{ session('success') ? 'alert-success' : 'alert-error' }} border-l-4">
                <p class="text-sm font-bold">{{ session('success') ?? session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10" id="dashboard-root" data-send-url="{{ route('send.reminder') }}">
        <div class="page-container">

            {{-- SECTION JATUH TEMPO --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="section-title flex items-center">
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
                                <div class="card flex w-full flex-col overflow-hidden border-t-4 {{ $tenant->days_left < 0 ? 'border-red-600' : 'border-orange-400' }} transition-all">
                                    <div class="flex h-full flex-col p-5">
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="pr-2">
                                                <h4 class="text-base font-bold {{ $tenant->days_left < 0 ? 'text-red-700' : 'text-stone-900' }} uppercase line-clamp-2">{{ $tenant->name }}</h4>
                                                <p class="text-sm text-stone-500 mt-1">Kamar {{ $tenant->room->room_number }}</p>
                                            </div>

                                            {{-- BADGE DINAMIS --}}
                                            <span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                                                {{ $tenant->days_left < 0 ? 'bg-red-600 text-white' : ($tenant->days_left == 0 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                                                @if($tenant->days_left < 0)
                                                    Telat {{ abs($tenant->days_left) }} Hari
                                                @elseif($tenant->days_left == 0)
                                                    Hari Ini
                                                @elseif($tenant->days_left == 1)
                                                    Besok
                                                @else
                                                    H-{{ $tenant->days_left }}
                                                @endif
                                            </span>
                                        </div>
                                        
                                        {{-- Box Informasi --}}
                                        <div class="mt-auto p-3 {{ $tenant->days_left < 0 ? 'bg-red-50' : 'bg-stone-50' }} rounded-xl">
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-stone-500">Sewa Bulanan:</span>
                                                <span class="font-bold tabular {{ $tenant->days_left < 0 ? 'text-red-700' : 'text-stone-800' }}">Rp {{ number_format($tenant->room->price, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between text-xs">
                                                <span class="text-stone-500">Jatuh Tempo:</span>
                                                <span class="{{ $tenant->days_left < 0 ? 'text-red-600 font-bold' : 'text-stone-700 font-medium' }}">
                                                    {{ $tenant->calculated_due_date->format('d M Y') }}
                                                </span>
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
                                                    data-days="{{ $tenant->days_left }}"
                                                    class="send-wa-btn btn w-full {{ $tenant->days_left < 0 ? 'bg-red-600 hover:bg-red-700' : 'bg-green-500 hover:bg-green-600' }} text-white text-xs font-bold rounded-xl shadow-sm">
                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                        {{ $tenant->days_left < 0 ? 'Tagih Sekarang!' : 'Kirim Reminder' }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h4 class="text-lg font-semibold text-stone-900">Belum Ada Tagihan</h4>
                        <p class="text-sm text-stone-500">Tidak ada tagihan yang masuk masa jatuh tempo dalam 7 hari ke depan.</p>
                    </div>
                @endif
            </div>

            {{-- RINGKASAN STATISTIK --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
                <div class="card flex items-center gap-4 p-5 animate-fade-in">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-500 shadow-soft">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-stone-500">Kamar Terisi</p>
                        <p class="text-2xl font-extrabold text-stone-900 tabular">{{ $occupiedRooms }}<span class="text-base font-semibold text-stone-400">/{{ $totalRooms }}</span></p>
                    </div>
                </div>

                <div class="card flex items-center gap-4 p-5 animate-fade-in [animation-delay:60ms]">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-500 shadow-soft">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-stone-500">Penghuni Aktif</p>
                        <p class="text-2xl font-extrabold text-stone-900 tabular">{{ $activeTenants }}</p>
                    </div>
                </div>

                <div class="card flex items-center gap-4 p-5 animate-fade-in [animation-delay:120ms]">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-yellow-500 shadow-soft">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-stone-500">Lunas Bulan Ini</p>
                        <p class="text-2xl font-extrabold text-stone-900 tabular">Rp {{ number_format($paymentsThisMonth, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="card flex items-center gap-4 p-5 animate-fade-in [animation-delay:180ms]">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-500 shadow-soft">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-stone-500">Jatuh Tempo</p>
                        <p class="text-2xl font-extrabold text-stone-900 tabular">{{ $pendingPayments }} <span class="text-xs font-normal text-stone-400">Orang</span></p>
                    </div>
                </div>
            </div>

            {{-- TABEL AKTIVITAS TERAKHIR --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Pembayaran Terbaru --}}
                <div class="card overflow-hidden">
                    <div class="card-header bg-stone-50/60">
                        <h3 class="section-title">Riwayat Pembayaran Terbaru</h3>
                        <a href="{{ route('payments.index') }}" 
                            class="btn-primary btn-sm">
                            Lihat Semua
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                    <div class="divide-y divide-stone-100">
                        @forelse($recentPayments as $finance)
                        <div class="flex items-center justify-between p-4 transition hover:bg-stone-50">
                            <div class="flex items-center">
                                <div class="avatar mr-3 h-10 w-10 bg-emerald-50 text-emerald-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2 2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-stone-800">{{ $finance->description ?? 'Pembayaran Sewa' }}</p>
                                    <p class="text-xs text-stone-500">{{ $finance->transaction_date->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-stone-800 tabular">Rp {{ number_format($finance->amount, 0, ',', '.') }}</p>
                                <span class="badge-success">
                                    Lunas
                                </span>
                            </div>
                        </div>
                        @empty
                        <p class="p-8 text-center text-stone-500 text-sm">Belum ada transaksi</p>
                        @endforelse
                    </div>
                </div>

                {{-- Keluhan Terbaru --}}
                <div class="card overflow-hidden">
                    <div class="card-header bg-stone-50/60">
                        <h3 class="section-title">Keluhan Terbaru</h3>
                        <a href="{{ route('complaints.index') }}" 
                            class="btn-primary btn-sm">
                            Lihat Semua
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                    <div class="divide-y divide-stone-100">
                        @forelse($recentComplaints as $complaint)
                        <div class="p-4 transition hover:bg-stone-50">
                            <div class="flex justify-between gap-2 mb-1">
                                <p class="text-sm font-bold text-stone-800 truncate">{{ $complaint->title }}</p>
                                <span class="badge {{ $complaint->status == 'open' ? 'bg-red-50 text-red-700 ring-red-200/70' : 'bg-blue-50 text-blue-700 ring-blue-200/70' }} shrink-0">
                                    {{ $complaint->status }}
                                </span>
                            </div>
                            <p class="text-xs text-stone-500">{{ $complaint->tenant->name ?? 'N/A' }} • Kamar {{ $complaint->room->room_number ?? '-' }}</p>
                        </div>
                        @empty
                        <p class="p-8 text-center text-stone-500 text-sm">Belum ada keluhan</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        @vite('resources/js/pages/dashboard.js')
    @endpush
</x-app-layout>