@extends('errors.layout')

@section('title', '500 - Gangguan Server')
@section('code', '500')

@section('badge')
<div class="inline-flex items-center gap-2 rounded-full bg-rose-50 border border-rose-200/80 px-3.5 py-1 text-xs font-bold text-rose-700">
    <span class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
    <span>ERROR 500 • SERVER ERROR</span>
</div>
@endsection

@section('illustration')
<div class="relative mx-auto flex items-center justify-center">
    <div class="text-[96px] sm:text-[116px] font-black tracking-tighter leading-none text-transparent bg-clip-text bg-gradient-to-r from-rose-600 via-amber-500 to-brand-500">
        500
    </div>
    <div class="absolute -bottom-2 flex items-center justify-center h-14 w-14 rounded-2xl bg-white shadow-lift border border-stone-100 -rotate-6 transform hover:rotate-0 transition-transform">
        <svg class="h-8 w-8 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    </div>
</div>
@endsection

@section('heading', 'Terjadi Gangguan pada Server')
@section('description', 'Maaf atas ketidaknyamanan Anda. Server kami sedang mengalami kendala teknis internal sementara. Tim kami telah mencatat kendala ini untuk segera diselesaikan.')

@section('actions')
<button type="button" onclick="window.location.reload();" class="btn-primary w-full sm:w-auto">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
    </svg>
    <span>Coba Muat Ulang</span>
</button>
@endsection

@section('details')
@if(config('app.debug') && isset($exception) && is_object($exception) && method_exists($exception, 'getMessage') && $exception->getMessage())
    <div class="mt-6 text-left">
        <details class="bg-stone-50 border border-stone-200 rounded-xl p-3 text-xs text-stone-600">
            <summary class="font-semibold text-stone-700 cursor-pointer select-none">
                Informasi Error (Mode Debug Aktif)
            </summary>
            <div class="mt-2 p-2 bg-stone-900 text-stone-200 rounded font-mono text-[11px] overflow-x-auto">
                {{ $exception->getMessage() }}
            </div>
        </details>
    </div>
@endif
@endsection

@section('help')
<div class="mt-8 pt-6 border-t border-stone-100 text-xs text-stone-500 flex items-center justify-center gap-1.5">
    <svg class="h-4 w-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span>Jika kendala terus berulang, silakan laporkan ke admin sistem Serrata Kost.</span>
</div>
@endsection
