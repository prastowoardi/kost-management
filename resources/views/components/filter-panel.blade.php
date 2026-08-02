@props(['reset' => null])

<div {{ $attributes->merge(['class' => 'card p-4']) }}>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{ $slot }}
    </div>

    @if($reset)
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-stone-100">
            <button type="submit" class="btn-primary btn-sm">
                Terapkan Filter
            </button>
            <a href="{{ $reset }}" class="btn-danger btn-sm bg-red-50 text-red-600 ring-1 ring-red-200 hover:bg-red-600 hover:text-white">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Reset
            </a>
            @isset($extra)
                <div class="ml-auto">{{ $extra }}</div>
            @endisset
        </div>
    @else
        @isset($extra)
            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-stone-100">
                <div class="ml-auto">{{ $extra }}</div>
            </div>
        @endisset
    @endif
</div>
