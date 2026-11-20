<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_tenant' => 'TNT-'.Str::upper(fake()->bothify('???###')),
            'nama_pic' => fake()->name(),
            'nama_perusahaan' => fake()->company(),
            'nib_npwp' => fake()->numerify('################'),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-50 years', '-21 years')->format('Y-m-d'),
            'jenis_kelamin' => fake()->randomElement(Tenant::JENIS_KELAMIN),
            'alamat' => fake()->address(),
            'no_hp' => fake()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'tanggal_mulai_sewa' => fake()->dateTimeBetween('-18 months', 'now')->format('Y-m-d'),
            'durasi_sewa' => fake()->randomElement([3, 6, 12, 24]),
            'status' => fake()->randomElement(Tenant::STATUS),
        ];
    }
}
