@extends('errors.layout')

@section('title', '402 - Pembayaran Diperlukan')
@section('code', '402')

@section('badge')
<div class="inline-flex items-center gap-2 rounded-full bg-brand-50 border border-brand-200/80 px-3.5 py-1 text-xs font-bold text-brand-700">
    <span class="h-2 w-2 rounded-full bg-brand-500"></span>
    <span>ERROR 402 • PAYMENT REQUIRED</span>
</div>
@endsection

@section('illustration')
<div class="relative mx-auto flex items-center justify-center">
    <div class="text-[96px] sm:text-[116px] font-black tracking-tighter leading-none text-transparent bg-clip-text bg-gradient-to-r from-brand-600 via-teal-500 to-emerald-400">
        402
    </div>
    <div class="absolute -bottom-2 flex items-center justify-center h-14 w-14 rounded-2xl bg-white shadow-lift border border-stone-100 -rotate-6">
        <svg class="h-7 w-7 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
    </div>
</div>
@endsection

@section('heading', 'Pembayaran Diperlukan')
@section('description', 'Akses ke layanan atau fitur ini memerlukan status pembayaran yang valid dan lunas.')

@section('actions')
<a href="{{ url('/') }}" class="btn-primary w-full sm:w-auto">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
    </svg>
    <span>Kembali ke Beranda</span>
</a>
@endsection
