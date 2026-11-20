<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_ruang' => 'RNG-'.fake()->unique()->numerify('###'),
            'nama_ruang' => fake()->randomElement(['Anggrek', 'Melati', 'Kenanga', 'Cendana', 'Meranti', 'Flamboyan'])
                .' '.fake()->numberBetween(1, 4),
            'tipe' => fake()->randomElement(Room::TIPE),
            'kapasitas' => fake()->numberBetween(2, 24),
            'lokasi' => 'Lantai '.fake()->numberBetween(1, 5),
            'harga_per_jam' => fake()->numberBetween(50, 400) * 1000,
            'fasilitas' => 'AC, Wi-Fi, Proyektor, Whiteboard',
            'status' => fake()->randomElement(Room::STATUS),
        ];
    }
}
