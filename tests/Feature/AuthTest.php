<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_screens_are_reachable(): void
    {
        $this->get('/login')->assertOk()->assertSee('Welcome Back');
        $this->get('/register')->assertOk()->assertSee('Sign Up to Get Started');
    }

    public function test_registration_stores_a_hashed_password(): void
    {
        $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();

        $user = User::where('email', 'budi@example.com')->firstOrFail();

        $this->assertNotSame('rahasia123', $user->password);
        $this->assertTrue(password_verify('rahasia123', $user->password));
    }

    public function test_registration_rejects_invalid_input(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post('/register', [
            'name' => '',
            'email' => 'taken@example.com',
            'password' => 'short',
        ])->assertSessionHasErrors(['name', 'email', 'password']);

        $this->assertGuest();
        $this->assertSame(1, User::count());
    }

    public function test_validation_errors_are_shown_on_the_register_screen(): void
    {
        $this->post('/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
        ]);

        $this->get('/register')
            ->assertOk()
            ->assertSee('The name field is required.')
            ->assertSee('The email field must be a valid email address.')
            ->assertSee('The password field must be at least 8 characters.');
    }

    public function test_login_succeeds_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'password' => password_hash('secret123', PASSWORD_DEFAULT),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => password_hash('secret123', PASSWORD_DEFAULT),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->get('/login')->assertSee('These credentials do not match our records.');

        $this->assertGuest();
    }

    public function test_authenticated_users_are_redirected_away_from_auth_screens(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/login')->assertRedirect('/dashboard');
        $this->actingAs($user)->get('/register')->assertRedirect('/dashboard');
    }

    public function test_logout_ends_the_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');

        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Logout');
    }
}
