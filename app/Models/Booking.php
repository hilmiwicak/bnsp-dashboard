<?php

namespace App\Models;

use App\Models\Concerns\HasSequentialCode;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'kode_pemesanan',
    'tenant_id',
    'room_id',
    'harga_per_jam',
    'tanggal',
    'jam_mulai',
    'jam_selesai',
    'jumlah_peserta',
    'status',
    'catatan',
])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory, HasSequentialCode;

    /**
     * Status pemesanan ruang.
     *
     * @var list<string>
     */
    public const STATUS = [
        'Menunggu',
        'Disetujui',
        'Ditolak',
        'Selesai',
    ];

    /**
     * Status yang dianggap memakai slot ruang.
     *
     * @var list<string>
     */
    public const STATUS_MENEMPATI = [
        'Menunggu',
        'Disetujui',
        'Selesai',
    ];

    /**
     * Status yang tampil pada jadwal penggunaan ruang.
     *
     * @var list<string>
     */
    public const STATUS_JADWAL = [
        'Disetujui',
        'Selesai',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah_peserta' => 'integer',
            'harga_per_jam' => 'integer',
        ];
    }

    /**
     * Kode pemesanan berikutnya, mis. PMN-001.
     */
    public static function nextKodePemesanan(): string
    {
        return static::nextCode('PMN', 'kode_pemesanan');
    }

    /**
     * Tenant yang mengajukan pemesanan.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Ruang yang dipesan.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Cari pemesanan berdasarkan kode, tenant, atau ruang.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function (Builder $query) use ($like) {
            $query->where('kode_pemesanan', 'like', $like)
                ->orWhereHas('tenant', fn (Builder $tenant) => $tenant
                    ->where('nama_pic', 'like', $like)
                    ->orWhere('nama_perusahaan', 'like', $like))
                ->orWhereHas('room', fn (Builder $room) => $room
                    ->where('nama_ruang', 'like', $like)
                    ->orWhere('kode_ruang', 'like', $like));
        });
    }

    /**
     * Pemesanan lain pada ruang dan tanggal yang sama yang jamnya bertabrakan.
     */
    public function scopeOverlapping(
        Builder $query,
        string $tanggal,
        string $jamMulai,
        string $jamSelesai,
        ?int $ignoreId = null,
    ): Builder {
        return $query
            ->whereDate('tanggal', $tanggal)
            ->whereIn('status', self::STATUS_MENEMPATI)
            ->where('jam_mulai', '<', self::normalizeTime($jamSelesai))
            ->where('jam_selesai', '>', self::normalizeTime($jamMulai))
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId));
    }

    /**
     * Jam mulai dalam format H:i untuk ditampilkan.
     */
    public function jamMulaiShort(): string
    {
        return substr((string) $this->jam_mulai, 0, 5);
    }

    /**
     * Jam selesai dalam format H:i untuk ditampilkan.
     */
    public function jamSelesaiShort(): string
    {
        return substr((string) $this->jam_selesai, 0, 5);
    }

    /**
     * Rentang jam pemakaian ruang.
     */
    public function rentang(): string
    {
        return $this->jamMulaiShort().' – '.$this->jamSelesaiShort();
    }

    /**
     * Warna badge yang mewakili status pemesanan.
     */
    public function statusTone(): string
    {
        return self::toneFor($this->status);
    }

    /**
     * Warna badge untuk sebuah status pemesanan.
     */
    public static function toneFor(string $status): string
    {
        return match ($status) {
            'Disetujui' => 'green',
            'Menunggu' => 'amber',
            'Ditolak' => 'rose',
            'Selesai' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Samakan panjang jam agar perbandingan antar kolom konsisten.
     */
    private static function normalizeTime(string $time): string
    {
        return strlen($time) === 5 ? $time.':00' : $time;
    }
}
