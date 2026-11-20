<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun demo: test@example.com / password. Dibuat sekali saja agar
        // `php artisan db:seed` aman dijalankan berulang kali.
        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => password_hash('password', PASSWORD_DEFAULT),
            ],
        );

        $this->call(PenyewaSeeder::class);
    }
}
