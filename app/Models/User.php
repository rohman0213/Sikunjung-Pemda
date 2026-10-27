<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'user';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_KARYAWAN = 'karyawan';

    public const ROLE_PEMOHON = 'pemohon';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Profil karyawan yang terhubung dengan akun ini.
     */
    public function profilKaryawan(): ?Karyawan
    {
        return Karyawan::where('user_id', $this->id)->first();
    }

    /**
     * Profil pemohon yang terhubung dengan akun ini.
     */
    public function profilPemohon(): ?Pemohon
    {
        return Pemohon::where('user_id', $this->id)->first();
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isKaryawan(): bool
    {
        return $this->role === self::ROLE_KARYAWAN;
    }

    public function isPemohon(): bool
    {
        return $this->role === self::ROLE_PEMOHON;
    }
}
