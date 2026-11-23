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
                'status' => 'Aktif',
                'catatan' => 'Termasuk alamat domisili usaha dan resepsionis.',
            ],
            [
                'nomor_kontrak' => 'KTR-002',
                'tenant' => 'TNT-002',
                'tanggal_mulai' => now()->subMonths(4)->toDateString(),
                'tanggal_berakhir' => now()->addMonths(8)->toDateString(),
                'status' => 'Aktif',
                'catatan' => null,
            ],
            [
                'nomor_kontrak' => 'KTR-003',
                'tenant' => 'TNT-003',
                'tanggal_mulai' => now()->subMonths(14)->toDateString(),
                'tanggal_berakhir' => now()->subMonths(2)->toDateString(),
                'status' => 'Berakhir',
                'catatan' => 'Tenant belum memperpanjang kontrak.',
            ],
            [
                'nomor_kontrak' => 'KTR-004',
                'tenant' => 'TNT-004',
                'tanggal_mulai' => now()->subMonths(2)->toDateString(),
                'tanggal_berakhir' => now()->addMonths(10)->toDateString(),
                'status' => 'Menunggu Verifikasi',
                'catatan' => null,
            ],
            [
                'nomor_kontrak' => 'KTR-005',
                'tenant' => 'TNT-005',
                'tanggal_mulai' => now()->subMonths(8)->toDateString(),
                'tanggal_berakhir' => now()->addMonths(4)->toDateString(),
                'status' => 'Aktif',
                'catatan' => null,
            ],
            [
                'nomor_kontrak' => 'KTR-006',
                'tenant' => 'TNT-008',
                'tanggal_mulai' => now()->subMonths(3)->toDateString(),
                'tanggal_berakhir' => now()->addMonths(9)->toDateString(),
                'status' => 'Dibatalkan',
                'catatan' => 'Dibatalkan karena tenant mengundurkan diri.',
            ],
            [
                'nomor_kontrak' => 'KTR-007',
                'tenant' => 'TNT-006',
                'tanggal_mulai' => now()->addWeeks(2)->toDateString(),
                'tanggal_berakhir' => now()->addWeeks(2)->addMonths(12)->toDateString(),
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
                    'status' => $row['status'],
                    'catatan' => $row['catatan'],
                ],
            );
        }
    }
}
