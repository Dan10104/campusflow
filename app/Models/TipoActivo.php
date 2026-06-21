<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoActivo extends Model
{
    protected $table = 'tipo_activo';
    
    protected $fillable = [
        'nombre',
    ];

    public function activos(): HasMany
    {
        return $this->hasMany(Activo::class, 'tipo_activo_id');
    }
}