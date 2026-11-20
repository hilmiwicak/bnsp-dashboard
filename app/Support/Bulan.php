<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Nama hari dan bulan dalam bahasa Indonesia.
 *
 * Aplikasi memakai locale "en", sehingga Carbon::translatedFormat() tidak
 * menghasilkan nama hari/bulan Indonesia. Helper ini menyediakannya.
 */
class Bulan
{
    /**
     * Nama bulan lengkap, ber-key nomor bulan.
     *
     * @var array<int, string>
     */
    public const NAMA = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Nama hari, ber-key hasil Carbon::dayOfWeek (0 = Minggu).
     *
     * @var list<string>
     */
    public const HARI = [
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
    ];

    /**
     * Nama bulan lengkap dari nomor bulan (1-12).
     */
    public static function nama(int $bulan): string
    {
        return self::NAMA[$bulan] ?? '';
    }

    /**
     * Nama bulan singkat, mis. "Nov".
     */
    public static function singkat(int $bulan): string
    {
        return mb_substr(self::nama($bulan), 0, 3);
    }

    /**
     * Ubah periode "YYYY-MM" menjadi label, mis. "November 2026".
     */
    public static function periode(?string $periode): string
    {
        if (! is_string($periode) || ! preg_match('/^(\d{4})-(\d{2})$/', $periode, $cocok)) {
            return (string) $periode;
        }

        return self::nama((int) $cocok[2]).' '.$cocok[1];
    }

    /**
     * Tanggal lengkap dengan nama hari, mis. "Senin, 24 November 2026".
     */
    public static function tanggalLengkap(Carbon|string $tanggal): string
    {
        $date = $tanggal instanceof Carbon ? $tanggal : Carbon::parse($tanggal);

        return self::HARI[$date->dayOfWeek].', '.$date->day.' '.self::nama($date->month).' '.$date->year;
    }
}
