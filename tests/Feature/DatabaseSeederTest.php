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
