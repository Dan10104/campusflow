<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    protected $table = 'sensores';
    
    protected $fillable = [
        'tipo',
        'hardware_id',
        'estado',
        'ultimo_heartbeat',
        'aula_codigo',
        'bateria',
        'senal',
    ];

    protected $casts = [
        'tipo' => 'string',
        'estado' => 'string',
        'ultimo_heartbeat' => 'datetime',
    ];

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class, 'aula_codigo', 'codigo');
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(EventoSensor::class, 'sensor_id');
    }
}