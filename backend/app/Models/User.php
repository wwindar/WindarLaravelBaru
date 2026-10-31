<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'bio', 'angkatan'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Tulisan milik user ini. */
    public function tulisan(): HasMany
    {
        return $this->hasMany(Tulisan::class);
    }

    /** Notifikasi in-app milik user ini. */
    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    /** Apakah user berstatus dosen/editor/admin? */
    public function isDosen(): bool
    {
        return in_array($this->role, ['dosen', 'admin'], true);
    }

    /** Apakah user berstatus mahasiswa/penulis? */
    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    /**
     * Alias — sama dengan isDosen(), mencakup role 'dosen' dan 'admin'.
     */
    public function isAdmin(): bool
    {
        return $this->isDosen();
    }
}
