<?php

namespace App\Models;

use App\Models\Concerns\HasSequentialCode;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

#[Fillable([
    'kode_tenant',
    'nama_pic',
    'nama_perusahaan',
    'nib_npwp',
    'tempat_lahir',
    'tanggal_lahir',
    'jenis_kelamin',
    'alamat',
    'no_hp',
    'email',
    'tanggal_mulai_sewa',
    'durasi_sewa',
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
     * Pilihan jenis kelamin untuk data PIC.
     *
     * @var list<string>
     */
    public const JENIS_KELAMIN = [
        'Laki-laki',
        'Perempuan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_mulai_sewa' => 'date',
            'durasi_sewa' => 'integer',
        ];
    }

    /**
     * Pemesanan ruang yang diajukan tenant ini.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
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
     * Tanggal berakhirnya masa sewa berdasarkan tanggal mulai dan durasi.
     */
    public function masaSewaBerakhir(): ?Carbon
    {
        if (! $this->tanggal_mulai_sewa) {
            return null;
        }

        return $this->tanggal_mulai_sewa->copy()->addMonths((int) $this->durasi_sewa);
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
