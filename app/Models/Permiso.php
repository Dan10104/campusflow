<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    protected $table = 'permisos';
    protected $primaryKey = 'codigo';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'icono',
        'ruta',
    ];

    /**
     * Relación: Un permiso pertenece a muchos roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'roles_permisos',
            'permiso_codigo',
            'rol_codigo',
            'codigo',
            'codigo'
        )->withTimestamps();
    }

    /**
     * Scope: Buscar permiso por nombre
     */
    public function scopePorNombre($query, string $nombre)
    {
        return $query->where('nombre', $nombre);
    }

    /**
     * Scope: Permisos de un módulo específico
     */
    public function scopePorModulo($query, string $modulo)
    {
        return $query->where('nombre', 'like', $modulo . '.%');
    }
}
