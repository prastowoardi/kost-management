@props(['paginator' => null, 'card' => false])

@php
    $p = $paginator->appends(request()->query());
    $current = $p->currentPage();
    $last = $p->lastPage();
    $start = max(1, $current - 2);
    $end = min($last, $current + 2);
@endphp

@if ($p->hasPages())
    <div @class([
        'px-5 py-4 sm:px-6',
        'border-t border-stone-100' => !$card,
        'rounded-xl sm:rounded-2xl border border-stone-100 bg-white shadow-sm' => $card,
    ])>
        <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
            <p class="order-2 text-sm text-stone-500 sm:order-1">
                Menampilkan
                <span class="font-semibold text-stone-700">{{ $p->firstItem() }}–{{ $p->lastItem() }}</span>
                dari
                <span class="font-semibold text-stone-700">{{ $p->total() }}</span>
                data
            </p>

            <nav class="order-1 flex max-w-full items-center gap-1 overflow-x-auto sm:order-2" aria-label="Pagination">
                @if ($p->onFirstPage())
                    <span aria-disabled="true" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-stone-50 text-stone-300 ring-1 ring-stone-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <a href="{{ $p->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-stone-600 ring-1 ring-stone-200 transition-colors hover:bg-stone-50 hover:text-stone-900">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif

                @if ($start > 1)
                    <a href="{{ $p->url(1) }}" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-stone-600 ring-1 ring-stone-200 transition-colors hover:bg-stone-50 hover:text-stone-900">1</a>
                    @if ($start > 2)
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center text-sm text-stone-400">…</span>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $current)
                        <span aria-current="page" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-sm font-bold text-white shadow-soft">{{ $i }}</span>
                    @else
                        <a href="{{ $p->url($i) }}" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-stone-600 ring-1 ring-stone-200 transition-colors hover:bg-stone-50 hover:text-stone-900">{{ $i }}</a>
                    @endif
                @endfor

                @if ($end < $last)
                    @if ($end < $last - 1)
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center text-sm text-stone-400">…</span>
                    @endif
                    <a href="{{ $p->url($last) }}" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-stone-600 ring-1 ring-stone-200 transition-colors hover:bg-stone-50 hover:text-stone-900">{{ $last }}</a>
                @endif

                @if ($p->hasMorePages())
                    <a href="{{ $p->nextPageUrl() }}" rel="next" aria-label="Berikutnya" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-stone-600 ring-1 ring-stone-200 transition-colors hover:bg-stone-50 hover:text-stone-900">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span aria-disabled="true" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-stone-50 text-stone-300 ring-1 ring-stone-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </nav>
        </div>
    </div>
@endif
