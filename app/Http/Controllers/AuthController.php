<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login screen.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Show the registration screen.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Validate the submitted credentials and start a session.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // password_verify() re-hashes the submitted password using the salt stored
        // next to the hash in the users table, so plain text is never compared.
        if (! $user || ! password_verify($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended($user->homePath());
    }

    /**
     * Persist a new user with a hashed password and sign them in.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => password_hash($validated['password'], PASSWORD_DEFAULT),
            // Pendaftar mandiri langsung berstatus tenant; data sewanya dibuat
            // lewat formulir pengajuan dan diverifikasi admin.
            'role' => UserRole::Tenant,
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('portal')->with('status', 'Your account has been created.');
    }

    /**
     * End the current session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out.');
    }
}
