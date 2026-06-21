<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aula extends Model
{
    protected $table = 'aulas';
    protected $primaryKey = 'codigo';
    
    protected $fillable = [
        'capacidad',
        'tipo',
        'estado',
        'qr_code',
        'modulo_codigo',
    ];

    protected $casts = [
        'tipo' => 'string',
        'estado' => 'string',
    ];

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'modulo_codigo', 'codigo');
    }

    public function caracteristicas(): BelongsToMany
    {
        return $this->belongsToMany(CaracteristicaAula::class, 'aula_caracteristicas', 'aula_codigo', 'caracteristica_id');
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'aula_codigo', 'codigo');
    }

    public function bloqueos(): HasMany
    {
        return $this->hasMany(BloqueoAula::class, 'aula_codigo', 'codigo');
    }

    public function sensores(): HasMany
    {
        return $this->hasMany(Sensor::class, 'aula_codigo', 'codigo');
    }
}