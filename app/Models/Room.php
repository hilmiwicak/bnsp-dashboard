<?php

namespace App\Models;

use App\Models\Concerns\HasSequentialCode;
use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'kode_ruang',
    'nama_ruang',
    'tipe',
    'kapasitas',
    'lokasi',
    'harga_per_jam',
    'fasilitas',
    'status',
])]
class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory, HasSequentialCode;

    /**
     * Jenis ruang yang tersedia pada kantor virtual.
     *
     * @var list<string>
     */
    public const TIPE = [
        'Meeting Room',
        'Private Office',
        'Virtual Office',
        'Coworking Space',
    ];

    /**
     * Status ketersediaan ruang.
     *
     * @var list<string>
     */
    public const STATUS = [
        'Tersedia',
        'Terpakai',
        'Perbaikan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
            'harga_per_jam' => 'integer',
        ];
    }

    /**
     * Kode ruang berikutnya untuk penambahan cepat, mis. RNG-007.
     */
    public static function nextKodeRuang(): string
    {
        return static::nextCode('RNG', 'kode_ruang');
    }

    /**
     * Pemesanan yang memakai ruang ini.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Cari ruang berdasarkan kode, nama, lokasi, atau tipe.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function (Builder $query) use ($like) {
            $query->where('kode_ruang', 'like', $like)
                ->orWhere('nama_ruang', 'like', $like)
                ->orWhere('lokasi', 'like', $like)
                ->orWhere('tipe', 'like', $like);
        });
    }

    /**
     * Warna badge yang mewakili status ruang.
     */
    public function statusTone(): string
    {
        return match ($this->status) {
            'Tersedia' => 'green',
            'Terpakai' => 'amber',
            'Perbaikan' => 'rose',
            default => 'gray',
        };
    }

    /**
     * Tarif sewa per jam dalam format rupiah.
     */
    public function hargaPerJam(): string
    {
        return 'Rp'.number_format($this->harga_per_jam, 0, ',', '.');
    }
}
