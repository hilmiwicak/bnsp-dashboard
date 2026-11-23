<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HalfHourSlot implements ValidationRule
{
    /**
     * Pastikan jam berada pada kelipatan 30 menit, mis. 09:00 atau 09:30.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match('/^([01]\d|2[0-3]):(00|30)$/', (string) $value) !== 1) {
            $fail('Jam harus dalam kelipatan 30 menit, mis. 09:00 atau 09:30.');
        }
    }
}
