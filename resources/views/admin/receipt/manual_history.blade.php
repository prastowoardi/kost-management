@extends('layouts.app')

@section('content')
<div class="py-12 bg-[#f8fafc]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Riwayat Kwitansi</h2>
                <p class="text-slate-500 mt-1 text-sm font-medium">Daftar seluruh kwitansi yang dibuat secara manual.</p>
            </div>
            <a href="{{ route('admin.receipt.create') }}" class="inline-flex items-center px-6 py-3 bg-teal-600 border border-transparent rounded-2xl font-bold text-white hover:bg-teal-700 transition shadow-lg shadow-teal-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Kwitansi Baru
            </a>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</p>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ $receipts->count() }}</p>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <p class="text-xs font-bold text-teal-500 uppercase tracking-wider">Total Dana Masuk</p>
                <p class="text-2xl font-black text-slate-800 mt-1">Rp {{ number_format($receipts->sum('total_amount'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <p class="text-xs font-bold text-orange-400 uppercase tracking-wider">Bulan Ini</p>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ $receipts->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">No. Invoice</th>
                            <th class="px-6 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Penyewa</th>
                            <th class="px-6 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Kamar</th>
                            <th class="px-6 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Total Bayar</th>
                            <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($receipts as $r)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-8 py-5">
                                <span class="font-mono text-sm font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg group-hover:bg-white transition-colors">
                                    {{ $r->invoice_number }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-2 font-semibold italic">{{ $r->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="font-extrabold text-slate-800">{{ $r->tenant_name }}</div>
                                <div class="text-xs text-teal-600 font-bold uppercase tracking-tighter">Periode {{ $r->period }}</div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center px-3 py-1 bg-teal-50 text-teal-700 rounded-full text-xs font-black border border-teal-100">
                                    No. {{ $r->room_number }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-sm font-black text-slate-800">Rp {{ number_format($r->total_amount, 0, ',', '.') }}</div>
                                <div class="flex items-center mt-1 text-[10px] text-green-500 font-bold uppercase">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Lunas
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <a href="{{ route('admin.receipt.print', $r->id) }}" class="inline-flex items-center justify-center p-2.5 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-teal-600 hover:border-teal-200 hover:bg-teal-50 transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-slate-50 p-6 rounded-full mb-4">
                                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <p class="text-slate-400 font-bold text-lg">Belum ada riwayat kwitansi manual.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection