@props([
    'series' => [],
    'labels' => [],
    'height' => 260,
    'format' => 'rupiah',
    'suffix' => '',
    'emptyText' => 'Belum ada data untuk ditampilkan.',
])

@php
    use App\Support\Chart;

    $count = count($labels);
    $hasData = $count > 0 && collect($series)->contains(fn($s) => collect($s['values'] ?? [])->sum() > 0);

    // Area plot (dalam koordinat viewBox, satuan relatif terhadap lebar total).
    $viewW = 640;
    $padL = 56;
    $padR = 14;
    $padT = 14;
    $padB = 30;

    $plotW = $viewW - $padL - $padR;
    $plotH = $height - $padT - $padB;
    $baselineY = $padT + $plotH;

    $maxValue = collect($series)->flatMap(fn($s) => $s['values'] ?? [])->max() ?? 0;

    $ticks = Chart::yTicks($maxValue);
    $axisMax = end($ticks);

    $xs = Chart::xPositions($count, $padL, $plotW);
    $xLabels = Chart::axisLabels($labels);
    $fmt = $format === 'percent' ? fn($v) => Chart::percent($v) : fn($v) => Chart::rupiah($v);

    $uid = 'trend-' . substr(md5(json_encode([$series, $labels])), 0, 8);
@endphp

<div class="chart" data-chart>
    @if (!$hasData)
        <div class="flex items-center justify-center text-sm text-stone-400" style="height: {{ $height }}px">
            {{ $emptyText }}
        </div>
    @else
        <svg viewBox="0 0 {{ $viewW }} {{ $height }}" class="h-auto w-full" role="img"
            aria-label="Grafik tren {{ $count }} periode terakhir">
            <defs>
                @foreach ($series as $i => $s)
                    <linearGradient id="{{ $uid }}-fill-{{ $i }}" x1="0" y1="0"
                        x2="0" y2="1">
                        <stop offset="0%" stop-color="{{ $s['color'] }}" stop-opacity="0.22" />
                        <stop offset="100%" stop-color="{{ $s['color'] }}" stop-opacity="0" />
                    </linearGradient>
                @endforeach
            </defs>

            {{-- Gridline + label sumbu Y --}}
            @foreach ($ticks as $tick)
                @php $y = Chart::yPosition($tick, $axisMax, $padT, $plotH); @endphp
                <line x1="{{ $padL }}" y1="{{ $y }}" x2="{{ $viewW - $padR }}"
                    y2="{{ $y }}" stroke="#e7e5e4" stroke-width="1" />
                <text x="{{ $padL - 10 }}" y="{{ $y + 4 }}" text-anchor="end"
                    class="fill-stone-400 text-[10px]">{{ $format === 'percent' ? Chart::percent($tick, 0) : Chart::compactNumber($tick) }}</text>
            @endforeach

            {{-- Label sumbu X --}}
            @foreach ($xLabels as $i => $meta)
                @if ($meta['show'])
                    <text x="{{ $xs[$i] }}" y="{{ $height - 10 }}" text-anchor="middle"
                        class="fill-stone-500 text-[10px]">{{ $meta['label'] }}</text>
                @endif
            @endforeach

            {{-- Area + garis per seri --}}
            @foreach ($series as $i => $s)
                @php
                    $points = [];
                    foreach ($s['values'] ?? [] as $j => $value) {
                        $points[] = [$xs[$j], Chart::yPosition($value, $axisMax, $padT, $plotH)];
                    }
                @endphp

                <path d="{{ Chart::areaPath($points, $baselineY) }}"
                    fill="url(#{{ $uid }}-fill-{{ $i }}" />
                <path d="{{ Chart::linePath($points) }}" fill="none" stroke="{{ $s['color'] }}"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />

                @foreach ($points as $j => [$x, $y])
                    <circle cx="{{ $x }}" cy="{{ $y }}" r="3.5" fill="#fff"
                        stroke="{{ $s['color'] }}" stroke-width="2" />
                @endforeach
            @endforeach

            {{-- Area hover per titik: memicu tooltip lewat JS --}}
            @foreach ($xs as $i => $x)
                @php
                    $slotW = $count > 1 ? $plotW / ($count - 1) : $plotW;
                    $half = $slotW / 2;
                    $rows = [];
                    foreach ($series as $s) {
                        $rows[] = [
                            'color' => $s['color'],
                            'label' => $s['label'],
                            'value' => $fmt($s['values'][$i] ?? 0) . $suffix,
                        ];
                    }
                @endphp
                <rect x="{{ max($padL, $x - $half) }}" y="{{ $padT }}" width="{{ min($slotW, $plotW) }}"
                    height="{{ $plotH }}" fill="transparent" class="chart-hit" data-chart-point
                    data-tooltip="{{ json_encode(['title' => $labels[$i] ?? '', 'rows' => $rows], JSON_UNESCAPED_UNICODE) }}" />
            @endforeach
        </svg>
    @endif

    @if ($hasData && count($series) > 0)
        <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2">
            @foreach ($series as $s)
                <span class="inline-flex items-center gap-2 text-xs font-medium text-stone-600">
                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $s['color'] }}"></span>
                    {{ $s['label'] }}
                </span>
            @endforeach
        </div>
    @endif
</div>
