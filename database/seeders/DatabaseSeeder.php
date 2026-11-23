<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Semua akun demo memakai password "password" dan dibuat dengan
     * firstOrCreate agar `php artisan db:seed` aman dijalankan berulang kali.
     */
    public function run(): void
    {
        // Data tenant lebih dulu, karena akun tenant perlu dihubungkan ke salah satunya.
        $this->call(TenantSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(BookingSeeder::class);
        $this->call(ContractSeeder::class);
        $this->call(InvoiceSeeder::class);

        $this->seedTenantAccounts();

        $this->seedAccount('admin@example.com', 'Admin User', UserRole::Admin);

        $this->seedAccount(
            'tenant@example.com',
            'Budi Santoso',
            UserRole::Tenant,
            Tenant::where('kode_tenant', 'TNT-001')->value('id'),
        );

        $this->seedAccount('pendaftar@example.com', 'Rina Wulandari', UserRole::Tenant);
    }

    /**
     * Beri setiap tenant contoh akun login PIC-nya sendiri, sama seperti yang
     * dilakukan TenantController saat admin menambah tenant dari Data Tenant.
     *
     * Akun dikunci pada email PIC, jadi menjalankan seeder berulang kali tidak
     * menggandakan akun dan tidak mengubah password yang sudah ada.
     */
    private function seedTenantAccounts(): void
    {
        Tenant::query()->each(function (Tenant $tenant): void {
            User::query()->firstOrCreate(
                ['email' => $tenant->email],
                [
                    'name' => $tenant->nama_pic,
                    'password' => password_hash('password', PASSWORD_DEFAULT),
                    'role' => UserRole::Tenant,
                    'tenant_id' => $tenant->id,
                ],
            );
        });
    }

    /**
     * Buat akun demo bila belum ada.
     */
    private function seedAccount(
        string $email,
        string $name,
        UserRole $role,
        ?int $tenantId = null,
    ): void {
        User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => $role,
                'tenant_id' => $tenantId,
            ],
        );
    }
}
