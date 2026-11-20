<?php

namespace App\Models\Concerns;

trait HasSequentialCode
{
    /**
     * Kode berurutan berikutnya berdasarkan angka terbesar yang sudah ada,
     * mis. RNG-001 ketika tabel masih kosong.
     */
    public static function nextCode(string $prefix, string $column, int $pad = 3): string
    {
        $terakhir = static::query()
            ->pluck($column)
            ->map(fn (string $kode) => (int) preg_replace('/\D+/', '', $kode))
            ->max() ?? 0;

        return $prefix.'-'.str_pad((string) ($terakhir + 1), $pad, '0', STR_PAD_LEFT);
    }
}
