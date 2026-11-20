<?php

namespace App\Enums;

/**
 * Jenis akun yang bisa masuk ke sistem kantor virtual.
 *
 * - Admin    : pengelola kantor virtual (operator), akses penuh.
 * - Tenant   : tenant aktif, terhubung ke satu baris tabel tenant.
 * - Customer : pendaftar baru yang belum memiliki data sewa.
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Tenant = 'tenant';
    case Customer = 'customer';

    /**
     * Label yang ditampilkan di antarmuka.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Tenant => 'Tenant',
            self::Customer => 'Customer',
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
