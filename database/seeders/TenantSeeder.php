<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Seed contoh data tenant kantor virtual.
     */
    public function run(): void
    {
        $rows = [
            [
                'kode_tenant' => 'TNT-001',
                'nama_pic' => 'Budi Santoso',
                'nama_perusahaan' => 'PT Nusantara Digital',
                'nib_npwp' => '8123456789012',
                'alamat' => 'Jl. Sudirman Kav. 52-53, Jakarta Selatan, 12190',
                'no_hp' => '081234567890',
                'email' => 'budi@nusantaradigital.co.id',
                'status' => 'Aktif',
            ],
            [
                'kode_tenant' => 'TNT-002',
                'nama_pic' => 'Siti Rahmawati',
                'nama_perusahaan' => 'CV Karya Mandiri',
                'nib_npwp' => '7345678901234',
                'alamat' => 'Jl. Asia Afrika No. 118, Bandung, 40112',
                'no_hp' => '081298765432',
                'email' => 'siti@karyamandiri.id',
                'status' => 'Aktif',
            ],
            [
                'kode_tenant' => 'TNT-003',
                'nama_pic' => 'Andi Pratama',
                'nama_perusahaan' => 'PT Sinar Konsultan',
                'nib_npwp' => null,
                'alamat' => 'Jl. Basuki Rahmat No. 45, Surabaya, 60271',
                'no_hp' => '081377889900',
                'email' => 'andi@sinarkonsultan.com',
                'status' => 'Aktif',
            ],
            [
                'kode_tenant' => 'TNT-004',
                'nama_pic' => 'Dewi Lestari',
                'nama_perusahaan' => 'Startup Kopi Kita',
                'nib_npwp' => '6590123456789',
                'alamat' => 'Jl. Malioboro No. 72, Yogyakarta, 55271',
                'no_hp' => '085712345678',
                'email' => 'dewi@kopikita.co',
                'status' => 'Menunggu Verifikasi',
            ],
            [
                'kode_tenant' => 'TNT-005',
                'nama_pic' => 'Rizky Hidayat',
                'nama_perusahaan' => 'PT Logistik Cepat',
                'nib_npwp' => '9012345678901',
                'alamat' => 'Jl. Gatot Subroto KM 7, Medan, 20112',
                'no_hp' => '081166778899',
                'email' => 'rizky@logistikcepat.id',
                'status' => 'Aktif',
            ],
            [
                'kode_tenant' => 'TNT-006',
                'nama_pic' => 'Maya Anggraini',
                'nama_perusahaan' => 'Biro Hukum Adil Sejahtera',
                'nib_npwp' => '4456789012345',
                'alamat' => 'Jl. Pandanaran No. 21, Semarang, 50241',
                'no_hp' => '082133445566',
                'email' => 'maya@adilsejahtera.co.id',
                'status' => 'Menunggu Verifikasi',
            ],
            [
                'kode_tenant' => 'TNT-007',
                'nama_pic' => 'Fajar Nugroho',
                'nama_perusahaan' => 'PT Arka Teknologi',
                'nib_npwp' => '5678901234567',
                'alamat' => 'Jl. Ijen No. 90, Malang, 65115',
                'no_hp' => '081255667788',
                'email' => 'fajar@arkateknologi.com',
                'status' => 'Nonaktif',
            ],
            [
                'kode_tenant' => 'TNT-008',
                'nama_pic' => 'Nadia Puspita',
                'nama_perusahaan' => 'Klinik Sehat Bersama',
                'nib_npwp' => '3345678901234',
                'alamat' => 'Jl. Teuku Umar No. 55, Denpasar, 80113',
                'no_hp' => '081799887766',
                'email' => 'nadia@sehatbersama.id',
                'status' => 'Aktif',
            ],
        ];

        foreach ($rows as $row) {
            Tenant::updateOrCreate(
                ['kode_tenant' => $row['kode_tenant']],
                $row,
            );
        }
    }
}
