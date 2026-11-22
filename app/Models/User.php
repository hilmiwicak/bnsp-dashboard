<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'tenant_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'role' => UserRole::class,
            // The 'hashed' cast is intentionally omitted: passwords are hashed with
            // password_hash() in AuthController, and casting would hash them twice.
        ];
    }

    /**
     * Data tenant yang terhubung dengan akun ini.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Apakah akun ini pengelola kantor virtual.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Apakah akun ini tenant aktif yang terhubung ke data sewa.
     */
    public function isTenant(): bool
    {
        return $this->role === UserRole::Tenant;
    }

    /**
     * Apakah akun ini memakai portal (semua role selain admin).
     */
    public function canAccessPortal(): bool
    {
        return ! $this->isAdmin();
    }

    /**
     * Halaman pertama yang dibuka setelah login, sesuai role akun.
     */
    public function homePath(): string
    {
        return $this->isAdmin() ? route('dashboard') : route('portal');
    }
}
