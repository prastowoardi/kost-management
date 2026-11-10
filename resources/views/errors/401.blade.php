@extends('errors.layout')

@section('title', '401 - Autentikasi Diperlukan')
@section('code', '401')

@section('badge')
<div class="inline-flex items-center gap-2 rounded-full bg-amber-50 border border-amber-200/80 px-3.5 py-1 text-xs font-bold text-amber-700">
    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
    <span>ERROR 401 • UNAUTHORIZED</span>
</div>
@endsection

@section('illustration')
<div class="relative mx-auto flex items-center justify-center">
    <div class="text-[96px] sm:text-[116px] font-black tracking-tighter leading-none text-transparent bg-clip-text bg-gradient-to-r from-amber-600 via-brand-500 to-teal-500">
        401
    </div>
    <div class="absolute -bottom-2 flex items-center justify-center h-14 w-14 rounded-2xl bg-white shadow-lift border border-stone-100 rotate-6">
        <svg class="h-7 w-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
        </svg>
    </div>
</div>
@endsection

@section('heading', 'Autentikasi Diperlukan')
@section('description', 'Anda harus masuk (login) terlebih dahulu untuk dapat mengakses halaman atau data ini.')

@section('actions')
<a href="{{ route('login') }}" class="btn-primary w-full sm:w-auto">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
    </svg>
    <span>Masuk ke Akun</span>
</a>

<a href="{{ url('/') }}" class="btn-secondary w-full sm:w-auto">
    <svg class="h-4 w-4 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
    </svg>
    <span>Kembali ke Beranda</span>
</a>
@endsection
