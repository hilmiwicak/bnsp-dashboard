<?php

namespace App\Models;

use App\Models\Concerns\HasSequentialCode;
use Database\Factories\ContractFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'nomor_kontrak',
    'tenant_id',
    'tanggal_mulai',
    'tanggal_berakhir',
    'nilai_sewa',
    'status',
    'catatan',
])]
class Contract extends Model
{
    /** @use HasFactory<ContractFactory> */
    use HasFactory, HasSequentialCode;

    /**
     * Status kontrak sewa.
     *
     * @var list<string>
     */
    public const STATUS = [
        'Aktif',
        'Menunggu Verifikasi',
        'Berakhir',
        'Dibatalkan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_berakhir' => 'date',
            'nilai_sewa' => 'integer',
        ];
    }

    /**
     * Nomor kontrak berikutnya, mis. KTR-001.
     */
    public static function nextKodeKontrak(): string
    {
        return static::nextCode('KTR', 'nomor_kontrak');
    }

    /**
     * Tenant yang menandatangani kontrak.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Cari kontrak berdasarkan nomor atau identitas tenant.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function (Builder $query) use ($like) {
            $query->where('nomor_kontrak', 'like', $like)
                ->orWhereHas('tenant', fn (Builder $tenant) => $tenant
                    ->where('nama_perusahaan', 'like', $like)
                    ->orWhere('nama_pic', 'like', $like));
        });
    }

    /**
     * Warna badge yang mewakili status kontrak.
     */
    public function statusTone(): string
    {
        return match ($this->status) {
            'Aktif' => 'green',
            'Menunggu Verifikasi' => 'amber',
            'Berakhir' => 'gray',
            'Dibatalkan' => 'rose',
            default => 'gray',
        };
    }

    /**
     * Nilai sewa dalam format rupiah.
     */
    public function nilaiSewa(): string
    {
        return 'Rp'.number_format($this->nilai_sewa, 0, ',', '.');
    }

    /**
     * Lama kontrak dalam satuan bulan.
     */
    public function durasiBulan(): int
    {
        if (! $this->tanggal_mulai || ! $this->tanggal_berakhir) {
            return 0;
        }

        return max(0, (int) $this->tanggal_mulai->diffInMonths($this->tanggal_berakhir));
    }
}
