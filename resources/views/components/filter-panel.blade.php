@props(['reset' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-slate-100 p-4']) }}>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{ $slot }}
    </div>

    @if($reset)
        <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-100">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded-lg hover:bg-slate-700 transition">
                Terapkan Filter
            </button>
            <a href="{{ $reset }}" class="px-3 py-2 text-xs font-bold rounded-lg bg-red-50 text-red-600 border border-red-200 hover:bg-red-600 hover:text-white hover:border-red-600 transition flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Reset
            </a>
            @isset($extra)
                <div class="ml-auto">{{ $extra }}</div>
            @endisset
        </div>
    @else
        @isset($extra)
            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-100">
                <div class="ml-auto">{{ $extra }}</div>
            </div>
        @endisset
    @endif
</div>
