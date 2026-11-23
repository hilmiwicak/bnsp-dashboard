<?php

namespace App\Support;

use App\Models\Booking;
use Illuminate\Support\Carbon;

/**
 * Perhitungan biaya pemakaian ruang seorang tenant pada satu periode tagihan.
 */
class TagihanRuang
{
    /**
     * Status pemesanan yang boleh ditagihkan. `Disetujui` mencakup pemesanan
     * yang disetujui tetapi belum berlangsung, `Selesai` yang sudah terpakai.
     *
     * @var list<string>
     */
    public const STATUS_DITAGIH = ['Disetujui', 'Selesai'];

    /**
     * Ringkasan biaya pemakaian ruang pada periode `YYYY-MM`.
     *
     * @return array{pemesanan: int, menit: int, total: int}
     */
    public static function ringkasan(int $tenantId, string $periode): array
    {
        $mulai = Carbon::createFromFormat('Y-m', $periode)->startOfMonth();

        // Batas atas eksklusif (awal bulan berikutnya). Perbandingan tanggal
        // langsung tidak aman karena kolom `date` bisa tersimpan sebagai string
        // panjang pada beberapa driver.
        $bookings = Booking::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', self::STATUS_DITAGIH)
            ->where('tanggal', '>=', $mulai->toDateString())
            ->where('tanggal', '<', $mulai->copy()->addMonthNoOverflow()->toDateString())
            ->with('room:id,harga_per_jam')
            ->get();

        $menit = 0;
        $total = 0;

        foreach ($bookings as $booking) {
            $durasi = self::menit($booking);
            $tarif = (int) ($booking->harga_per_jam ?? $booking->room?->harga_per_jam ?? 0);

            $menit += $durasi;
            $total += (int) round($tarif * $durasi / 60);
        }

        return [
            'pemesanan' => $bookings->count(),
            'menit' => $menit,
            'total' => $total,
        ];
    }

    /**
     * Durasi satu pemesanan dalam menit.
     */
    public static function menit(Booking $booking): int
    {
        $tanggal = $booking->tanggal->toDateString();

        $mulai = Carbon::parse($tanggal.' '.$booking->jam_mulai);
        $selesai = Carbon::parse($tanggal.' '.$booking->jam_selesai);

        return max(0, (int) round(($selesai->getTimestamp() - $mulai->getTimestamp()) / 60));
    }

    /**
     * Total menit dalam bentuk jam, mis. "5,5 jam".
     */
    public static function jam(int $menit): string
    {
        $teks = number_format($menit / 60, 1, ',', '.');

        return str_replace(',0', '', $teks).' jam';
    }
}
