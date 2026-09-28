@props([
    'slices' => [],
    'centerLabel' => '',
    'centerValue' => '',
    'size' => 180,
    'emptyText' => 'Belum ada data kamar.',
])

@php
    $total = collect($slices)->sum('value');
    $hasData = $total > 0;

    $radius = 60;
    $stroke = 18;
    $circumference = 2 * M_PI * $radius;
    $offset = 0.0;

    $uid = 'donut-' . substr(md5(json_encode($slices)), 0, 8);
@endphp

<div class="chart flex flex-col items-center gap-5 sm:flex-row sm:items-center sm:gap-6" data-chart>
    @if (!$hasData)
        <div
            class="flex items-center justify-center rounded-xl border-2 border-dashed border-stone-200 px-6 py-10 text-center text-sm text-stone-400">
            {{ $emptyText }}
        </div>
    @else
        <div class="relative shrink-0" style="width: {{ $size }}px; height: {{ $size }}px">
            <svg viewBox="0 0 160 160" class="h-full w-full -rotate-90" role="img" aria-label="Komposisi kamar">
                <circle cx="80" cy="80" r="{{ $radius }}" fill="none" stroke="#f5f5f4"
                    stroke-width="{{ $stroke }}" />

                @foreach ($slices as $slice)
                    @php
                        $value = (float) $slice['value'];
                        $length = $total > 0 ? ($value / $total) * $circumference : 0;
                        $dash = $length . ' ' . max(0, $circumference - $length);
                    @endphp
                    <circle cx="80" cy="80" r="{{ $radius }}" fill="none"
                        stroke="{{ $slice['color'] }}" stroke-width="{{ $stroke }}"
                        stroke-dasharray="{{ $dash }}" stroke-dashoffset="{{ round(-$offset, 2) }}"
                        data-tooltip="{{ json_encode(
                            [
                                'title' => $slice['label'],
                                'rows' => [
                                    [
                                        'color' => $slice['color'],
                                        'label' => $slice['label'],
                                        'value' => $value . ' kamar (' . round($total > 0 ? ($value / $total) * 100 : 0) . '%)',
                                    ],
                                ],
                            ],
                            JSON_UNESCAPED_UNICODE,
                        ) }}" />
                    @php $offset += $length; @endphp
                @endforeach
            </svg>

            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-2xl font-extrabold tabular text-stone-900">{{ $centerValue }}</span>
                @if ($centerLabel)
                    <span
                        class="text-[11px] font-medium uppercase tracking-wide text-stone-400">{{ $centerLabel }}</span>
                @endif
            </div>
        </div>

        <ul class="w-full min-w-0 flex-1 space-y-2.5">
            @foreach ($slices as $slice)
                <li class="flex items-center justify-between gap-3 text-sm">
                    <span class="inline-flex min-w-0 items-center gap-2">
                        <span class="h-2.5 w-2.5 shrink-0 rounded-full"
                            style="background-color: {{ $slice['color'] }}"></span>
                        <span class="truncate text-stone-600">{{ $slice['label'] }}</span>
                    </span>
                    <span class="shrink-0 font-semibold tabular text-stone-800">
                        {{ $slice['value'] }}
                        <span class="font-normal text-stone-400">/
                            {{ round($total > 0 ? ($slice['value'] / $total) * 100 : 0) }}%</span>
                    </span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
