<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_tagihan' => 'INV-'.fake()->unique()->numerify('####'),
            'tenant_id' => Tenant::factory(),
            'periode' => now()->format('Y-m'),
            'jumlah' => fake()->numberBetween(10, 40) * 100000,
            'jatuh_tempo' => now()->endOfMonth()->toDateString(),
            'tanggal_bayar' => null,
            'status' => 'Belum Dibayar',
        ];
    }

    /**
     * Tagihan yang sudah dibayar.
     */
    public function lunas(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Lunas',
            'tanggal_bayar' => now()->subDays(3)->toDateString(),
        ]);
    }

    /**
     * Tagihan yang sudah melewati jatuh tempo.
     */
    public function terlambat(): static
    {
        return $this->state(fn (array $attributes) => [
            'jatuh_tempo' => now()->subWeeks(2)->toDateString(),
            'status' => 'Terlambat',
        ]);
    }
}
