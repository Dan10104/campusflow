<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'codigo';
    
    protected $fillable = [
        'nombre',
    ];

    /**
     * Relación: Un rol tiene muchos permisos
     */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(
            Permiso::class,
            'roles_permisos',
            'rol_id',
            'permiso_id',
            'codigo',
            'codigo'
        )->withTimestamps();
    }

    /**
     * Relación: Un rol pertenece a muchos usuarios
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'usuarios_roles',
            'rol_codigo',
            'usuario_id',
            'codigo',
            'id'
        )->withTimestamps();
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function tienePermiso(string $nombrePermiso): bool
    {
        return $this->permisos()->where('nombre', $nombrePermiso)->exists();
    }

    /**
     * Asignar un permiso al rol
     */
    public function asignarPermiso(int $permisoId): void
    {
        $this->permisos()->syncWithoutDetaching([$permisoId]);
    }

    /**
     * Remover un permiso del rol
     */
    public function removerPermiso(int $permisoId): void
    {
        $this->permisos()->detach($permisoId);
    }

    /**
     * Sincronizar permisos (reemplaza todos los permisos existentes)
     */
    public function sincronizarPermisos(array $permisosIds): void
    {
        $this->permisos()->sync($permisosIds);
    }

    /**
     * Scope: Buscar rol por nombre
     */
    public function scopePorNombre($query, string $nombre)
    {
        return $query->where('nombre', $nombre);
    }

}
