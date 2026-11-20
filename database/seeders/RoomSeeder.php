<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Seed contoh data ruang dan fasilitas kantor virtual.
     */
    public function run(): void
    {
        $rows = [
            [
                'kode_ruang' => 'RNG-001',
                'nama_ruang' => 'Anggrek 1',
                'tipe' => 'Meeting Room',
                'kapasitas' => 12,
                'lokasi' => 'Lantai 3',
                'harga_per_jam' => 150000,
                'fasilitas' => 'AC, Wi-Fi, Proyektor, Whiteboard, Video Conference',
                'status' => 'Tersedia',
            ],
            [
                'kode_ruang' => 'RNG-002',
                'nama_ruang' => 'Anggrek 2',
                'tipe' => 'Meeting Room',
                'kapasitas' => 8,
                'lokasi' => 'Lantai 3',
                'harga_per_jam' => 120000,
                'fasilitas' => 'AC, Wi-Fi, TV LED, Whiteboard',
                'status' => 'Terpakai',
            ],
            [
                'kode_ruang' => 'RNG-003',
                'nama_ruang' => 'Melati Suite',
                'tipe' => 'Private Office',
                'kapasitas' => 6,
                'lokasi' => 'Lantai 4',
                'harga_per_jam' => 200000,
                'fasilitas' => 'AC, Wi-Fi, Meja kerja, Lemari arsip, Telepon',
                'status' => 'Tersedia',
            ],
            [
                'kode_ruang' => 'RNG-004',
                'nama_ruang' => 'Kenanga Hall',
                'tipe' => 'Meeting Room',
                'kapasitas' => 30,
                'lokasi' => 'Lantai 5',
                'harga_per_jam' => 350000,
                'fasilitas' => 'AC, Wi-Fi, Sound system, Proyektor, Panggung kecil',
                'status' => 'Tersedia',
            ],
            [
                'kode_ruang' => 'RNG-005',
                'nama_ruang' => 'Coworking Cendana',
                'tipe' => 'Coworking Space',
                'kapasitas' => 20,
                'lokasi' => 'Lantai 2',
                'harga_per_jam' => 75000,
                'fasilitas' => 'AC, Wi-Fi, Meja komunal, Pantry, Printer',
                'status' => 'Tersedia',
            ],
            [
                'kode_ruang' => 'RNG-006',
                'nama_ruang' => 'Virtual Desk 01',
                'tipe' => 'Virtual Office',
                'kapasitas' => 2,
                'lokasi' => 'Lantai 1',
                'harga_per_jam' => 50000,
                'fasilitas' => 'Wi-Fi, Meja resepsionis, Alamat surat menyurat',
                'status' => 'Perbaikan',
            ],
        ];

        foreach ($rows as $row) {
            Room::updateOrCreate(
                ['kode_ruang' => $row['kode_ruang']],
                $row,
            );
        }
    }
}
