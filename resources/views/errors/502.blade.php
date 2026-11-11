@extends('errors.layout')

@section('title', '502 - Gangguan Gateway Server')
@section('code', '502')

@section('badge')
<div class="inline-flex items-center gap-2 rounded-full bg-rose-50 border border-rose-200/80 px-3.5 py-1 text-xs font-bold text-rose-700">
    <span class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
    <span>ERROR 502 • BAD GATEWAY</span>
</div>
@endsection

@section('illustration')
<div class="relative mx-auto flex items-center justify-center">
    <div class="text-[96px] sm:text-[116px] font-black tracking-tighter leading-none text-transparent bg-clip-text bg-gradient-to-r from-rose-600 via-amber-500 to-brand-500">
        502
    </div>
    <div class="absolute -bottom-2 flex items-center justify-center h-14 w-14 rounded-2xl bg-white shadow-lift border border-stone-100 -rotate-6">
        <svg class="h-7 w-7 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
</div>
@endsection

@section('heading', 'Gangguan Gateway Server')
@section('description', 'Server menerima respon yang tidak valid dari server upstream. Mohon muat ulang halaman atau coba beberapa saat lagi.')

@section('actions')
<button type="button" onclick="window.location.reload();" class="btn-primary w-full sm:w-auto">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
    </svg>
    <span>Coba Muat Ulang</span>
</button>

<a href="{{ url('/') }}" class="btn-secondary w-full sm:w-auto">
    <svg class="h-4 w-4 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
    </svg>
    <span>Kembali ke Beranda</span>
</a>
@endsection
