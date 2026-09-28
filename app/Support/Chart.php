<?php

namespace App\Support;

/**
 * Utilitas geometri & format untuk menggambar chart SVG langsung dari Blade.
 *
 * Sengaja tanpa dependensi charting library: chart dirender server-side sebagai
 * SVG murni, jadi tampil instan, bisa di-print, dan tidak menambah bundle JS.
 */
class Chart
{
    /**
     * Bulatkan nilai maksimum sumbu Y ke angka "bulat" yang enak dibaca
     * (mis. 1.200.000 -> 1.500.000), lalu bagi rata menjadi N tick.
     *
     * @return float[] Nilai tick dari 0 ke maksimum, urut naik.
     */
    public static function yTicks(float|int $max, int $ticks = 4): array
    {
        $max = (float) $max;
        $ticks = max(2, $ticks);

        if ($max <= 0) {
            return array_map(fn(int $i) => (float) $i, range(0, $ticks));
        }

        $nice = self::niceCeil($max);
        $step = $nice / $ticks;

        return array_map(fn(int $i) => round($step * $i, 2), range(0, $ticks));
    }

    /**
     * Pembulatan ke atas ke kelipatan "bulat" terdekat (1, 2, 2.5, 5, atau 10 * 10^n).
     */
    public static function niceCeil(float $value): float
    {
        if ($value <= 0) {
            return 0.0;
        }

        $magnitude = 10 ** floor(log10($value));
        $normalized = $value / $magnitude;

        $step = match (true) {
            $normalized <= 1 => 1.0,
            $normalized <= 2 => 2.0,
            $normalized <= 2.5 => 2.5,
            $normalized <= 5 => 5.0,
            default => 10.0,
        };

        return $step * $magnitude;
    }

    /**
     * Koordinat X titik data, tersebar rata pada lebar area plot.
     *
     * @return float[]
     */
    public static function xPositions(int $count, float $left, float $width): array
    {
        if ($count <= 0) {
            return [];
        }

        if ($count === 1) {
            return [round($left + $width / 2, 2)];
        }

        $step = $width / ($count - 1);

        return array_map(fn(int $i) => round($left + $step * $i, 2), range(0, $count - 1));
    }

    /**
     * Ubah nilai menjadi koordinat Y (sumbu terbalik: nilai besar = Y kecil).
     */
    public static function yPosition(float|int $value, float $max, float $top, float $height): float
    {
        if ($max <= 0) {
            return round($top + $height, 2);
        }

        $ratio = min(1.0, max(0.0, $value / $max));

        return round($top + $height - ($height * $ratio), 2);
    }

    /**
     * Bangun path SVG (polyline) dari sekumpulan koordinat.
     */
    public static function linePath(array $points): string
    {
        if ($points === []) {
            return '';
        }

        $path = array_map(
            fn(array $point, int $i) => ($i === 0 ? 'M' : 'L') . $point[0] . ' ' . $point[1],
            $points,
            array_keys($points)
        );

        return implode(' ', $path);
    }

    /**
     * Area tertutup di bawah garis, untuk gradient fill.
     */
    public static function areaPath(array $points, float $baselineY): string
    {
        if ($points === []) {
            return '';
        }

        $first = $points[0][0];
        $last = $points[count($points) - 1][0];

        return self::linePath($points)
            . ' L' . $last . ' ' . $baselineY
            . ' L' . $first . ' ' . $baselineY
            . ' Z';
    }

    /**
     * Potong label sumbu X agar tidak bertumpuk; kembalikan label + flag tampil.
     *
     * @return array<int, array{label: string, show: bool}>
     */
    public static function axisLabels(array $labels, int $maxVisible = 12): array
    {
        $count = count($labels);

        $every = $count > $maxVisible ? (int) ceil($count / $maxVisible) : 1;

        return array_map(
            fn(string $label, int $i) => [
                'label' => $label,
                // Selalu tampilkan titik pertama & terakhir sebagai jangkar.
                'show' => $i % $every === 0 || $i === $count - 1,
            ],
            $labels,
            array_keys($labels)
        );
    }

    /**
     * Sumbu Y ringkas dalam satuan Indonesia: rb / jt / M.
     */
    public static function compactNumber(float|int $value): string
    {
        $value = (float) $value;
        $sign = $value < 0 ? '-' : '';
        $value = abs($value);

        return match (true) {
            $value >= 1_000_000_000 => $sign . number_format($value / 1_000_000_000, 1, ',', '.') . ' M',
            $value >= 1_000_000 => $sign . number_format($value / 1_000_000, 1, ',', '.') . ' jt',
            $value >= 1_000 => $sign . number_format($value / 1_000, 0, ',', '.') . ' rb',
            default => $sign . number_format($value, 0, ',', '.'),
        };
    }

    /**
     * Persentase ringkas: `75%`, `33,3%`, `100%`.
     *
     * Nol di belakang koma desimal dibuang, tapi nol di bagian bulat tidak
     * boleh ikut hilang (harus tetap "100%", bukan "1%").
     */
    public static function percent(float|int $value, int $decimals = 1): string
    {
        $formatted = number_format((float) $value, $decimals, ',', '.');

        if ($decimals > 0 && str_contains($formatted, ',')) {
            $formatted = rtrim(rtrim($formatted, '0'), ',');
        }

        return $formatted . '%';
    }

    /**
     * Rupiah penuh, mengikuti format yang dipakai di seluruh aplikasi.
     */
    public static function rupiah(float|int $value): string
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }
}
