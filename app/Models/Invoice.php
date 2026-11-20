<?php

namespace App\Models;

use App\Models\Concerns\HasSequentialCode;
use App\Support\Bulan;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'nomor_tagihan',
    'tenant_id',
    'periode',
    'jumlah',
    'jatuh_tempo',
    'tanggal_bayar',
    'status',
])]
class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory, HasSequentialCode;

    /**
     * Status pembayaran tagihan.
     *
     * @var list<string>
     */
    public const STATUS = [
        'Belum Dibayar',
        'Lunas',
        'Terlambat',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'jatuh_tempo' => 'date',
            'tanggal_bayar' => 'date',
        ];
    }

    /**
     * Nomor tagihan berikutnya, mis. INV-001.
     */
    public static function nextKodeTagihan(): string
    {
        return static::nextCode('INV', 'nomor_tagihan');
    }

    /**
     * Tenant yang ditagih.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Cari tagihan berdasarkan nomor, periode, atau identitas tenant.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function (Builder $query) use ($like) {
            $query->where('nomor_tagihan', 'like', $like)
                ->orWhere('periode', 'like', $like)
                ->orWhereHas('tenant', fn (Builder $tenant) => $tenant
                    ->where('nama_perusahaan', 'like', $like)
                    ->orWhere('nama_pic', 'like', $like));
        });
    }

    /**
     * Tagihan yang belum dibayar maupun yang sudah terlambat.
     */
    public function scopeBelumLunas(Builder $query): Builder
    {
        return $query->whereIn('status', ['Belum Dibayar', 'Terlambat']);
    }

    /**
     * Warna badge yang mewakili status tagihan.
     */
    public function statusTone(): string
    {
        return match ($this->status) {
            'Lunas' => 'green',
            'Belum Dibayar' => 'amber',
            'Terlambat' => 'rose',
            default => 'gray',
        };
    }

    /**
     * Jumlah tagihan dalam format rupiah.
     */
    public function jumlahRupiah(): string
    {
        return 'Rp'.number_format($this->jumlah, 0, ',', '.');
    }

    /**
     * Label periode, mis. "November 2026".
     */
    public function periodeLabel(): string
    {
        return Bulan::periode($this->periode);
    }
}
