@extends('errors.layout')

@section('title', View::yieldContent('title', 'Terjadi Kendala'))
@section('code', View::yieldContent('code', 'Error'))
@section('heading', View::yieldContent('title', 'Terjadi Kendala'))
@section('description', View::yieldContent('message', 'Terjadi kendala saat memproses permintaan Anda. Silakan coba beberapa saat lagi.'))

@section('badge')
<div class="inline-flex items-center gap-2 rounded-full bg-brand-50 border border-brand-200/80 px-3.5 py-1 text-xs font-bold text-brand-700">
    <span class="h-2 w-2 rounded-full bg-brand-500"></span>
    <span>STATUS @yield('code', 'ERROR')</span>
</div>
@endsection
