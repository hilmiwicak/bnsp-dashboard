<?php

namespace App\Enums;

/**
 * Jenis akun yang bisa masuk ke sistem kantor virtual.
 *
 * - Admin  : pengelola kantor virtual (pemilik/operator), akses penuh.
 * - Tenant : penyewa kantor virtual. Akun boleh belum terhubung ke data
 *            sewa selama pengajuannya belum diverifikasi admin.
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Tenant = 'tenant';

    /**
     * Label yang ditampilkan di antarmuka.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Tenant => 'Tenant',
        };
    }

    /**
     * Semua nilai role, dipakai untuk validasi dan query.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
