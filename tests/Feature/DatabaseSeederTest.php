<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_is_safe_to_run_more_than_once(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(1, User::where('email', 'admin@example.com')->count());
        $this->assertSame(8, Tenant::count());
        $this->assertSame(11, User::count());
    }

    public function test_every_seeded_tenant_has_a_login_account(): void
    {
        $this->seed();

        Tenant::query()->each(function (Tenant $tenant): void {
            $this->assertDatabaseHas('users', [
                'email' => $tenant->email,
                'name' => $tenant->nama_pic,
                'tenant_id' => $tenant->id,
            ]);
        });
    }

    public function test_a_tenant_account_can_log_in_to_the_portal(): void
    {
        $this->seed();

        $tenant = Tenant::where('kode_tenant', 'TNT-002')->firstOrFail();

        $this->post('/login', [
            'email' => $tenant->email,
            'password' => 'password',
        ])->assertRedirect('/portal');

        $this->assertAuthenticated();
    }

    public function test_the_demo_account_can_log_in(): void
    {
        $this->seed();

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
    }
}
