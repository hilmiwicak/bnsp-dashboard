<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Seed contoh pemesanan ruang oleh tenant.
     */
    public function run(): void
    {
        $rows = [
            [
                'kode_pemesanan' => 'PMN-001',
                'tenant' => 'TNT-001',
                'room' => 'RNG-001',
                'tanggal' => now()->addDays(2)->toDateString(),
                'jam_mulai' => '09:00',
                'jam_selesai' => '11:00',
                'jumlah_peserta' => 10,
                'status' => 'Disetujui',
                'catatan' => 'Rapat koordinasi proyek kuartal keempat.',
            ],
            [
                'kode_pemesanan' => 'PMN-002',
                'tenant' => 'TNT-002',
                'room' => 'RNG-002',
                'tanggal' => now()->addDays(3)->toDateString(),
                'jam_mulai' => '13:00',
                'jam_selesai' => '15:00',
                'jumlah_peserta' => 6,
                'status' => 'Menunggu',
                'catatan' => 'Presentasi proposal klien baru.',
            ],
            [
                'kode_pemesanan' => 'PMN-003',
                'tenant' => 'TNT-003',
                'room' => 'RNG-004',
                'tanggal' => now()->addDays(5)->toDateString(),
                'jam_mulai' => '10:00',
                'jam_selesai' => '14:00',
                'jumlah_peserta' => 25,
                'status' => 'Disetujui',
                'catatan' => 'Seminar internal perusahaan.',
            ],
            [
                'kode_pemesanan' => 'PMN-004',
                'tenant' => 'TNT-005',
                'room' => 'RNG-005',
                'tanggal' => now()->subDays(4)->toDateString(),
                'jam_mulai' => '08:00',
                'jam_selesai' => '12:00',
                'jumlah_peserta' => 8,
                'status' => 'Selesai',
                'catatan' => null,
            ],
            [
                'kode_pemesanan' => 'PMN-005',
                'tenant' => 'TNT-004',
                'room' => 'RNG-003',
                'tanggal' => now()->addDays(7)->toDateString(),
                'jam_mulai' => '15:00',
                'jam_selesai' => '17:00',
                'jumlah_peserta' => 4,
                'status' => 'Menunggu',
                'catatan' => 'Diskusi rencana ekspansi.',
            ],
            [
                'kode_pemesanan' => 'PMN-006',
                'tenant' => 'TNT-008',
                'room' => 'RNG-001',
                'tanggal' => now()->subDays(10)->toDateString(),
                'jam_mulai' => '09:00',
                'jam_selesai' => '10:00',
                'jumlah_peserta' => 5,
                'status' => 'Selesai',
                'catatan' => null,
            ],
        ];

        foreach ($rows as $row) {
            $tenantId = Tenant::where('kode_tenant', $row['tenant'])->value('id');
            $roomId = Room::where('kode_ruang', $row['room'])->value('id');

            if (! $tenantId || ! $roomId) {
                continue;
            }

            Booking::updateOrCreate(
                ['kode_pemesanan' => $row['kode_pemesanan']],
                [
                    'tenant_id' => $tenantId,
                    'room_id' => $roomId,
                    'tanggal' => $row['tanggal'],
                    'jam_mulai' => $row['jam_mulai'],
                    'jam_selesai' => $row['jam_selesai'],
                    'jumlah_peserta' => $row['jumlah_peserta'],
                    'status' => $row['status'],
                    'catatan' => $row['catatan'],
                ],
            );
        }
    }
}
