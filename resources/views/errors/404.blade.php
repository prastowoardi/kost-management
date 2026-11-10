@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan')
@section('code', '404')

@section('badge')
<div class="inline-flex items-center gap-2 rounded-full bg-brand-50 border border-brand-200/80 px-3.5 py-1 text-xs font-bold text-brand-700">
    <span class="h-2 w-2 rounded-full bg-brand-500 animate-ping"></span>
    <span>ERROR 404 • ROUTE NOT FOUND</span>
</div>
@endsection

@section('illustration')
<div class="relative mx-auto flex items-center justify-center">
    <div class="text-[96px] sm:text-[116px] font-black tracking-tighter leading-none text-transparent bg-clip-text bg-gradient-to-r from-brand-600 via-teal-500 to-emerald-400">
        404
    </div>
    <div class="absolute -bottom-2 flex items-center justify-center h-14 w-14 rounded-2xl bg-white shadow-lift border border-stone-100 rotate-6 transform hover:rotate-0 transition-transform">
        <svg class="h-7 w-7 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
        </svg>
    </div>
</div>
@endsection

@section('heading', 'Ups! Halaman Tidak Ditemukan')
@section('description', 'Halaman atau tautan yang Anda tuju mungkin sudah dipindahkan, dihapus, atau alamat URL yang dimasukkan salah.')

