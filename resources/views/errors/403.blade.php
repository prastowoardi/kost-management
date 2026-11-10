@extends('errors.layout')

@section('title', '403 - Akses Ditolak')
@section('code', '403')

@section('badge')
<div class="inline-flex items-center gap-2 rounded-full bg-rose-50 border border-rose-200/80 px-3.5 py-1 text-xs font-bold text-rose-700">
    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
    <span>ERROR 403 • FORBIDDEN</span>
</div>
@endsection

@section('illustration')
<div class="relative mx-auto flex items-center justify-center">
    <div class="text-[96px] sm:text-[116px] font-black tracking-tighter leading-none text-transparent bg-clip-text bg-gradient-to-r from-rose-600 via-amber-500 to-brand-500">
        403
    </div>
    <div class="absolute -bottom-2 flex items-center justify-center h-14 w-14 rounded-2xl bg-white shadow-lift border border-stone-100 rotate-6">
        <svg class="h-7 w-7 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
    </div>
</div>
@endsection

@section('heading', 'Akses Tidak Diizinkan')

@php
    $customMessage = (isset($exception) && is_object($exception) && method_exists($exception, 'getMessage') && $exception->getMessage() && $exception->getMessage() !== 'This action is unauthorized.')
        ? $exception->getMessage()
        : 'Maaf, akun Anda tidak memiliki hak akses atau role yang sesuai untuk membuka halaman ini.';
@endphp

@section('description', $customMessage)
