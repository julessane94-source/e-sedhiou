<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN   = 'admin';
    public const ROLE_AGENT   = 'agent';
    public const ROLE_SUPERVISEUR = 'superviseur';
    public const ROLE_CITOYEN = 'citoyen';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'first_name',
        'last_name',
        'phone',
        'address',
        'birth_date',
        'birth_place',
        'register_number',
        'citizen_number',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'birth_date'        => 'date',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
        ];
    }

    public static function normalizeRoleValue(?string $role): string
    {
        return strtolower(trim((string) $role));
    }

    public function setRoleAttribute($value): void
    {
        $this->attributes['role'] = self::normalizeRoleValue((string) $value);
    }

    // --- JWT interface ---
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'role'  => self::normalizeRoleValue((string) $this->role),
            'email' => $this->email,
        ];
    }

    // --- Rôles helpers ---
    public function isAdmin(): bool   { return self::normalizeRoleValue((string) $this->role) === self::ROLE_ADMIN; }
    public function isAgent(): bool   { return self::normalizeRoleValue((string) $this->role) === self::ROLE_AGENT; }
    public function isSuperviseur(): bool { return self::normalizeRoleValue((string) $this->role) === self::ROLE_SUPERVISEUR; }
    public function isCitoyen(): bool { return self::normalizeRoleValue((string) $this->role) === self::ROLE_CITOYEN; }

    // --- Relations ---
    public function demandes(): HasMany
    {
        return $this->hasMany(Demande::class, 'user_id');
    }

    public function assignedDemandes(): HasMany
    {
        return $this->hasMany(Demande::class, 'agent_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(AgentAttendance::class, 'user_id');
    }

    public function markedAttendances(): HasMany
    {
        return $this->hasMany(AgentAttendance::class, 'marked_by');
    }
}
