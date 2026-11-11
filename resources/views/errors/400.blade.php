@extends('errors.layout')

@section('title', '400 - Permintaan Tidak Valid')
@section('code', '400')

@section('badge')
<div class="inline-flex items-center gap-2 rounded-full bg-amber-50 border border-amber-200/80 px-3.5 py-1 text-xs font-bold text-amber-700">
    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
    <span>ERROR 400 • BAD REQUEST</span>
</div>
@endsection

@section('illustration')
<div class="relative mx-auto flex items-center justify-center">
    <div class="text-[96px] sm:text-[116px] font-black tracking-tighter leading-none text-transparent bg-clip-text bg-gradient-to-r from-amber-600 via-brand-500 to-teal-500">
        400
    </div>
    <div class="absolute -bottom-2 flex items-center justify-center h-14 w-14 rounded-2xl bg-white shadow-lift border border-stone-100 rotate-6">
        <svg class="h-7 w-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    </div>
</div>
@endsection

@section('heading', 'Permintaan Tidak Valid')
@section('description', 'Server tidak dapat memproses permintaan ini karena format atau parameter data yang dikirimkan tidak sesuai.')

@section('actions')
<a href="{{ url('/') }}" class="btn-primary w-full sm:w-auto">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
    </svg>
    <span>Kembali ke Beranda</span>
</a>

<button type="button" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ url('/') }}'; }" class="btn-secondary w-full sm:w-auto">
    <svg class="h-4 w-4 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
    </svg>
    <span>Halaman Sebelumnya</span>
</button>
@endsection
