<?php

namespace Tests\Feature;

use App\Models\Penyewa;
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

        $this->assertSame(1, User::where('email', 'test@example.com')->count());
        $this->assertSame(8, Penyewa::count());
    }

    public function test_the_demo_account_can_log_in(): void
    {
        $this->seed();

        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
    }
}
