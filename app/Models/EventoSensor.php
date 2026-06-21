<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoSensor extends Model
{
    protected $table = 'eventos_sensor';
    protected $primaryKey = 'codigo';
    
    protected $fillable = [
        'sensor_id',
        'tipo_evento',
        'registrado_en',
        'payload_json',
    ];

    protected $casts = [
        'registrado_en' => 'datetime',
        'payload_json' => 'array',
    ];

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class, 'sensor_id');
    }
}
