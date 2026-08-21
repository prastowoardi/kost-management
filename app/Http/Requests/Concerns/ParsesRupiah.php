<?php

namespace App\Http\Requests\Concerns;

trait ParsesRupiah
{
    /**
     * Sanitasi input rupiah ("Rp 1.500.000" -> 1500000).
     * Mengembalikan null jika input tidak mengandung digit sama sekali,
     * agar gagal di validasi 'required' alih-alih diam-diam menjadi 0.
     */
    protected function parseRupiah(mixed $value): ?int
    {
        if ($value === null || ! preg_match('/\d/', (string) $value)) {
            return null;
        }

        return (int) preg_replace('/[^0-9]/', '', (string) $value);
    }
}
