<?php

namespace App\Models;

use App\Models\Concerns\HasSequentialCode;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'kode_tenant',
    'nama_pic',
    'nama_perusahaan',
    'nib_npwp',
    'alamat',
    'no_hp',
    'email',
    'status',
])]
class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory, HasSequentialCode;

    /**
     * Status keanggotaan tenant.
     *
     * @var list<string>
     */
    public const STATUS = [
        'Aktif',
        'Menunggu Verifikasi',
        'Nonaktif',
    ];

    /**
     * Pemesanan ruang yang diajukan tenant ini.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Seluruh kontrak sewa yang pernah dimiliki tenant.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Kontrak aktif terbaru milik tenant. Menjadi sumber periode sewa yang berlaku.
     */
    public function activeContract(): HasOne
    {
        return $this->hasOne(Contract::class)
            ->where('status', 'Aktif')
            ->orderByDesc('tanggal_mulai');
    }

    /**
     * Kontrak terbaru yang masih berlaku atau menunggu verifikasi.
     */
    public function currentContract(): HasOne
    {
        return $this->hasOne(Contract::class)
            ->whereIn('status', ['Aktif', 'Menunggu Verifikasi'])
            ->orderByDesc('tanggal_mulai');
    }

    /**
     * Kode tenant berikutnya untuk pengajuan mandiri, mis. TNT-009.
     */
    public static function nextKodeTenant(): string
    {
        return static::nextCode('TNT', 'kode_tenant');
    }

    /**
     * Cari tenant berdasarkan kode, nama PIC, perusahaan, email, atau nomor HP.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function (Builder $query) use ($like) {
            $query->where('kode_tenant', 'like', $like)
                ->orWhere('nama_pic', 'like', $like)
                ->orWhere('nama_perusahaan', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('no_hp', 'like', $like);
        });
    }

    /**
     * Inisial nama PIC untuk ditampilkan pada tabel.
     */
    public function inisial(): string
    {
        return collect(explode(' ', (string) $this->nama_pic))
            ->filter()
            ->map(fn (string $part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');
    }

    /**
     * Warna badge yang mewakili status tenant.
     */
    public function statusTone(): string
    {
        return match ($this->status) {
            'Aktif' => 'green',
            'Menunggu Verifikasi' => 'amber',
            'Nonaktif' => 'gray',
            default => 'gray',
        };
    }
}
