<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role_id' => 'integer',
    ];

    // Relación con rol
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Relación con conductor (si aplica)
    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    // Verificar si es administrador
    public function isAdmin(): bool
    {
        return $this->role && $this->role->isAdmin();
    }

    // Verificar si es gestor
    public function isManager(): bool
    {
        return $this->role && $this->role->isManager();
    }

    // Verificar si es chofer
    public function isDriver(): bool
    {
        return $this->role && $this->role->isDriver();
    }

    // Verificar si es cliente
    public function isClient(): bool
    {
        // Normalize the stored role name by trimming whitespace and converting to lowercase
        if (! $this->role || ! $this->role->name) {
            return false;
        }

        $normalized = trim(strtolower($this->role->name));

        return $normalized === 'cliente';
    }

    /**
     * Comprueba si el usuario tiene uno de los roles dados.
     * Acepta string (un rol) o array de roles.
     */
    public function hasRole(string|array $roles): bool
    {
        if (! $this->role || ! $this->role->name) {
            return false;
        }

        // Normalize the user's role name by trimming whitespace and converting to lowercase
        $userRoleName = trim(strtolower($this->role->name));

        // Normalize each of the roles passed in for comparison
        $normalizedRoles = array_map(function ($role) {
            return trim(strtolower($role));
        }, is_array($roles) ? $roles : [$roles]);

        return in_array($userRoleName, $normalizedRoles, true);
    }

    // Obtener nombre del rol
    public function getRoleNameAttribute(): string
    {
        return $this->role ? $this->role->name : 'Sin rol';
    }
}
