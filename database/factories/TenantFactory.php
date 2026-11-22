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
            'alamat' => fake()->address(),
            'no_hp' => fake()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'status' => fake()->randomElement(Tenant::STATUS),
        ];
    }
}
