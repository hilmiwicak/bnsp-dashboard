<?php

namespace App\Rules;

use App\Models\Room;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FitsRoomCapacity implements ValidationRule
{
    /**
     * @param  int|null  $roomId  Ruang yang dipilih pada permintaan ini.
     */
    public function __construct(private ?int $roomId) {}

    /**
     * Pastikan jumlah peserta tidak melebihi kapasitas ruang.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Ruang yang tidak valid sudah ditangani aturan exists pada room_id.
        if (! $this->roomId) {
            return;
        }

        $kapasitas = Room::query()->whereKey($this->roomId)->value('kapasitas');

        if ($kapasitas !== null && (int) $value > (int) $kapasitas) {
            $fail("Jumlah peserta melebihi kapasitas ruang ({$kapasitas} orang).");
        }
    }
}
