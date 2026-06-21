<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modulo extends Model
{
    protected $table = 'modulos';
    protected $primaryKey = 'codigo';
    
    protected $fillable = [
        'nombre',
        'ubicacion',
        'facultad_codigo',
    ];

    public function facultad(): BelongsTo
    {
        return $this->belongsTo(Facultad::class, 'facultad_codigo', 'codigo');
    }

    public function aulas(): HasMany
    {
        return $this->hasMany(Aula::class, 'modulo_codigo', 'codigo');
    }
}