<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'identifier', // NIM for Mahasiswa, NIDN for Dosen/Dekan/Warek
        'fakultas_id',
        'prodi_id',
        'avatar_url',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($user) {
            try {
                if (Schema::hasColumn($user->getTable(), 'uuid') && empty($user->uuid)) {
                    $user->uuid = (string) Str::uuid();
                }
                if (Schema::hasColumn($user->getTable(), 'username') && empty($user->username)) {
                    $user->username = explode('@', (string) $user->email)[0] ?: (string) Str::random(8);
                }
            } catch (\Throwable) {
                // Ignore if schema check fails
            }
        });
    }

    public function profile()
    {
        return $this->hasOne(AcademicProfile::class, 'user_id', 'id');
    }

    public function getIdentifierAttribute(): ?string
    {
        if (isset($this->attributes['identifier'])) {
            return $this->attributes['identifier'];
        }

        try {
            return $this->profile?->identifier_number ?? $this->username ?? $this->email;
        } catch (\Throwable) {
            return $this->attributes['username'] ?? $this->attributes['email'] ?? null;
        }
    }

    public function getFakultasIdAttribute(): ?string
    {
        if (isset($this->attributes['fakultas_id'])) {
            return $this->attributes['fakultas_id'];
        }

        try {
            return $this->profile?->faculty_id ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function getProdiIdAttribute(): ?string
    {
        if (isset($this->attributes['prodi_id'])) {
            return $this->attributes['prodi_id'];
        }

        try {
            return $this->profile?->study_program_id ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function isMahasiswa(): bool
    {
        return $this->role === UserRole::MAHASISWA;
    }

    public function isKaprodi(): bool
    {
        return in_array($this->role, [UserRole::KAPRODI, UserRole::DOSEN, UserRole::DOSEN_PEMBIMBING], true);
    }

    public function isDosen(): bool
    {
        return in_array($this->role, [UserRole::DOSEN, UserRole::DOSEN_PEMBIMBING], true);
    }

    public function isDekan(): bool
    {
        return $this->role === UserRole::DEKAN;
    }

    public function isKeuangan(): bool
    {
        return $this->role === UserRole::KEUANGAN;
    }

    public function isWarekII(): bool
    {
        return in_array($this->role, [UserRole::WAREK_II, UserRole::WAREK, UserRole::PIMPINAN], true);
    }

    public function isWarek(): bool
    {
        return in_array($this->role, [UserRole::WAREK, UserRole::WAREK_II, UserRole::PIMPINAN], true);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function hasRole(UserRole|string $role): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $val = $role instanceof UserRole ? $role->value : $role;

        return match ($val) {
            'mahasiswa' => $this->isMahasiswa(),
            'kaprodi' => $this->isKaprodi(),
            'dosen', 'dosen_pembimbing' => $this->isDosen() || $this->isKaprodi(),
            'dekan' => $this->isDekan(),
            'keuangan' => $this->isKeuangan(),
            'warek', 'warek_ii', 'warek2', 'pimpinan' => $this->isWarek(),
            'admin' => $this->isAdmin(),
            default => $this->role?->value === $val,
        };
    }
}
