<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CaracteristicaAula extends Model
{
    protected $table = 'caracteristicas_aula';
    
    protected $fillable = [
        'nombre',
    ];

    public function aulas(): BelongsToMany
    {
        return $this->belongsToMany(Aula::class, 'aula_caracteristicas', 'caracteristica_id', 'aula_codigo');
    }
}

