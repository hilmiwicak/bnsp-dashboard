<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_kontrak' => 'KTR-'.fake()->unique()->numerify('####'),
            'tenant_id' => Tenant::factory(),
            'tanggal_mulai' => now()->subMonths(3)->toDateString(),
            'tanggal_berakhir' => now()->addMonths(9)->toDateString(),
            'nilai_sewa' => fake()->numberBetween(12, 60) * 500000,
            'status' => 'Aktif',
            'catatan' => null,
        ];
    }

    /**
     * Kontrak yang sudah selesai masa berlakunya.
     */
    public function berakhir(): static
    {
        return $this->state(fn (array $attributes) => [
            'tanggal_mulai' => now()->subYears(2)->toDateString(),
            'tanggal_berakhir' => now()->subMonths(2)->toDateString(),
            'status' => 'Berakhir',
        ]);
    }
}
