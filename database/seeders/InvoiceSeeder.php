<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Seed tagihan bulanan tenant untuk enam periode terakhir.
     *
     * Data sengaja memuat tagihan lunas dan belum lunas agar grafik
     * pendapatan pada dashboard punya isi.
     */
    public function run(): void
    {
        $tenants = Tenant::query()->orderBy('kode_tenant')->limit(4)->get();

        if ($tenants->isEmpty()) {
            return;
        }

        $nominal = [2500000, 1800000, 2100000, 1200000];

        $nomor = 1;

        foreach ([5, 4, 3, 2, 1, 0] as $bulanLalu) {
            $periode = now()->subMonths($bulanLalu);

            foreach ($tenants as $index => $tenant) {
                $status = $bulanLalu === 0 ? 'Belum Dibayar' : 'Lunas';

                // Satu tagihan bulan lalu dibiarkan menunggak agar status
                // "Terlambat" ikut terwakili pada data contoh.
                if ($bulanLalu === 1 && $index === 0) {
                    $status = 'Terlambat';
                }

                Invoice::updateOrCreate(
                    ['nomor_tagihan' => 'INV-'.str_pad((string) $nomor, 3, '0', STR_PAD_LEFT)],
                    [
                        'tenant_id' => $tenant->id,
                        'periode' => $periode->format('Y-m'),
                        'jumlah' => $nominal[$index] ?? 1500000,
                        'jatuh_tempo' => $periode->copy()->endOfMonth()->toDateString(),
                        'tanggal_bayar' => $status === 'Lunas'
                            ? $periode->copy()->day(10)->toDateString()
                            : null,
                        'status' => $status,
                    ],
                );

                $nomor++;
            }
        }
    }
}
