@props([
    'bars' => [],
    'format' => 'rupiah',
    'emptyText' => 'Tidak ada tunggakan. Semua penghuni sudah lunas.',
])

@php
    use App\Support\Chart;

    $rows = array_values(array_filter($bars));
    $hasData = collect($rows)->contains(fn($r) => ($r['value'] ?? 0) > 0);

    $labelW = 132;
    $valueW = 92;
    $barMax = collect($rows)->max('value') ?: 1;

    $rowH = 42;
    $barH = 24;

    $render = fn(float $v) => $format === 'percent' ? Chart::percent($v) : Chart::compactNumber($v);
@endphp

<div class="chart" data-chart>
    @if (!$hasData)
        <div
            class="flex items-center justify-center rounded-xl border-2 border-dashed border-stone-200 px-6 py-10 text-center text-sm text-stone-400">
            {{ $emptyText }}
        </div>
    @else
        <div class="space-y-1.5">
            @foreach ($rows as $row)
                @php
                    $value = (float) ($row['value'] ?? 0);
                    $pct = $value > 0 ? max(2, round(($value / $barMax) * 100, 1)) : 0;
                    $color = $row['color'] ?? '#a8a29e';
                    $meta = $row['meta'] ?? ($row['count'] ?? 0) . ' penghuni';
                    $tooltip = json_encode(
                        [
                            'title' => $row['label'],
                            'rows' => [
                                [
                                    'color' => $color,
                                    'label' => $row['label'],
                                    'value' => $format === 'percent' ? $render($value) : Chart::rupiah($value),
                                ],
                            ],
                        ],
                        JSON_UNESCAPED_UNICODE,
                    );
                @endphp
                <div class="flex items-center gap-3" style="height: {{ $rowH }}px">
                    <div class="shrink-0 text-right" style="width: {{ $labelW }}px">
                        <p class="truncate text-xs font-semibold text-stone-700">{{ $row['label'] }}</p>
                        <p class="text-[11px] text-stone-400">{{ $meta }}</p>
                    </div>

                    <div class="relative min-w-0 flex-1 rounded-lg bg-stone-100 transition-colors"
                        style="height: {{ $barH }}px" data-chart-point data-tooltip="{{ $tooltip }}">
                        <div class="absolute inset-y-0 left-0 rounded-lg transition-all duration-500"
                            style="width: {{ $pct }}%; background-color: {{ $color }}"></div>
                    </div>

                    <div class="shrink-0 text-right" style="width: {{ $valueW }}px">
                        <span class="text-xs font-bold tabular text-stone-800">{{ $render($value) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
