<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Seed contoh kontrak sewa tenant.
     */
    public function run(): void
    {
        $rows = [
            [
                'nomor_kontrak' => 'KTR-001',
                'tenant' => 'TNT-001',
                'tanggal_mulai' => now()->subMonths(6)->toDateString(),
                'tanggal_berakhir' => now()->addMonths(6)->toDateString(),
                'nilai_sewa' => 18000000,
                'status' => 'Aktif',
                'catatan' => 'Termasuk alamat domisili usaha dan resepsionis.',
            ],
            [
                'nomor_kontrak' => 'KTR-002',
                'tenant' => 'TNT-002',
                'tanggal_mulai' => now()->subMonths(4)->toDateString(),
                'tanggal_berakhir' => now()->addMonths(8)->toDateString(),
                'nilai_sewa' => 24000000,
                'status' => 'Aktif',
                'catatan' => null,
            ],
            [
                'nomor_kontrak' => 'KTR-003',
                'tenant' => 'TNT-003',
                'tanggal_mulai' => now()->subMonths(14)->toDateString(),
                'tanggal_berakhir' => now()->subMonths(2)->toDateString(),
                'nilai_sewa' => 15000000,
                'status' => 'Berakhir',
                'catatan' => 'Tenant belum memperpanjang kontrak.',
            ],
            [
                'nomor_kontrak' => 'KTR-004',
                'tenant' => 'TNT-004',
                'tanggal_mulai' => now()->subMonths(2)->toDateString(),
                'tanggal_berakhir' => now()->addMonths(10)->toDateString(),
                'nilai_sewa' => 30000000,
                'status' => 'Menunggu Verifikasi',
                'catatan' => null,
            ],
            [
                'nomor_kontrak' => 'KTR-005',
                'tenant' => 'TNT-005',
                'tanggal_mulai' => now()->subMonths(8)->toDateString(),
                'tanggal_berakhir' => now()->addMonths(4)->toDateString(),
                'nilai_sewa' => 21000000,
                'status' => 'Aktif',
                'catatan' => null,
            ],
            [
                'nomor_kontrak' => 'KTR-006',
                'tenant' => 'TNT-008',
                'tanggal_mulai' => now()->subMonths(3)->toDateString(),
                'tanggal_berakhir' => now()->addMonths(9)->toDateString(),
                'nilai_sewa' => 12000000,
                'status' => 'Dibatalkan',
                'catatan' => 'Dibatalkan karena tenant mengundurkan diri.',
            ],
            [
                'nomor_kontrak' => 'KTR-007',
                'tenant' => 'TNT-006',
                'tanggal_mulai' => now()->addWeeks(2)->toDateString(),
                'tanggal_berakhir' => now()->addWeeks(2)->addMonths(12)->toDateString(),
                'nilai_sewa' => 0,
                'status' => 'Menunggu Verifikasi',
                'catatan' => 'Pengajuan sewa menunggu verifikasi admin.',
            ],
        ];

        foreach ($rows as $row) {
            $tenantId = Tenant::where('kode_tenant', $row['tenant'])->value('id');

            if (! $tenantId) {
                continue;
            }

            Contract::updateOrCreate(
                ['nomor_kontrak' => $row['nomor_kontrak']],
                [
                    'tenant_id' => $tenantId,
                    'tanggal_mulai' => $row['tanggal_mulai'],
                    'tanggal_berakhir' => $row['tanggal_berakhir'],
                    'nilai_sewa' => $row['nilai_sewa'],
                    'status' => $row['status'],
                    'catatan' => $row['catatan'],
                ],
            );
        }
    }
}
