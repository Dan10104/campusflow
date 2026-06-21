<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre', 'ci', 'email', 'password', 'rol_id', 'fcm_token'
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

    protected $table = 'users';
    protected $primaryKey = 'codigo';
    public $timestamps = false;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'usuarios_roles',
            'usuario_id',
            'rol_id',
            'codigo',
            'codigo'
        )->withTimestamps();
    }

    /**
     * Verificar si el usuario es Administrador (General o de Aulas)
     * General ID = 1, Admin Aulas/Activos ID = 4
     */
    public function esAdmin(): bool
    {
        return $this->roles()->whereIn('rol_id', [1, 4])->exists();
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function tieneRol(string $nombreRol): bool
    {
        return $this->roles()->where('nombre', $nombreRol)->exists();
    }

    /**
     * Verificar si el usuario tiene alguno de los roles especificados
     */
    public function tieneAlgunRol(array $roles): bool
    {
        return $this->roles()->whereIn('nombre', $roles)->exists();
    }

    /**
     * Verificar si el usuario tiene todos los roles especificados
     */
    public function tieneTodosLosRoles(array $roles): bool
    {
        return $this->roles()->whereIn('nombre', $roles)->count() === count($roles);
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     * (verifica a través de sus roles)
     */
    public function tienePermiso(string $nombrePermiso): bool
    {
        foreach ($this->roles as $rol) {
            if ($rol->tienePermiso($nombrePermiso)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verificar si el usuario tiene alguno de los permisos especificados
     */
    public function tieneAlgunPermiso(array $permisos): bool
    {
        foreach ($permisos as $permiso) {
            if ($this->tienePermiso($permiso)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verificar si el usuario tiene todos los permisos especificados
     */
    public function tieneTodosLosPermisos(array $permisos): bool
    {
        foreach ($permisos as $permiso) {
            if (!$this->tienePermiso($permiso)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Asignar un rol al usuario
     */
    public function asignarRol(int $rolId): void
    {
        $this->roles()->syncWithoutDetaching([$rolId]);
    }

    /**
     * Remover un rol del usuario
     */
    public function removerRol(int $rolId): void
    {
        $this->roles()->detach($rolId);
    }

    /**
     * Sincronizar roles (reemplaza todos los roles existentes)
     */
    public function sincronizarRoles(array $rolesIds): void
    {
        $this->roles()->sync($rolesIds);
    }

    /**
     * Obtener todos los permisos del usuario (a través de sus roles)
     */
    public function permisos()
    {
        return $this->roles->flatMap(function ($rol) {
            return $rol->permisos;
        })->unique('codigo');
    }

    /**
     * Scope: Filtrar usuarios por rol
     */
    public function scopeConRol($query, string $nombreRol)
    {
        return $query->whereHas('roles', function ($q) use ($nombreRol) {
            $q->where('nombre', $nombreRol);
        });
    }

    /**
     * Scope: Filtrar usuarios por permiso
     */
    public function scopeConPermiso($query, string $nombrePermiso)
    {
        return $query->whereHas('roles.permisos', function ($q) use ($nombrePermiso) {
            $q->where('nombre', $nombrePermiso);
        });
    }
}
