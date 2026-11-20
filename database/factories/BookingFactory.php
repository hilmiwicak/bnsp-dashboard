<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mulai = fake()->numberBetween(8, 16);

        return [
            'kode_pemesanan' => 'PMN-'.fake()->unique()->numerify('####'),
            'tenant_id' => Tenant::factory(),
            'room_id' => Room::factory(),
            'tanggal' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'jam_mulai' => str_pad((string) $mulai, 2, '0', STR_PAD_LEFT).':00',
            'jam_selesai' => str_pad((string) ($mulai + 1), 2, '0', STR_PAD_LEFT).':00',
            'jumlah_peserta' => fake()->numberBetween(2, 10),
            'status' => 'Menunggu',
            'catatan' => null,
        ];
    }

    /**
     * Pemesanan yang sudah disetujui pengelola.
     */
    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Disetujui',
        ]);
    }
}
