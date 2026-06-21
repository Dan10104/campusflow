<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReservaActivos extends Model
{
    protected $table = 'reservas_activos';
    
    protected $fillable = [
        'usuario_id',
        'activo_codigo',
        'inicio_previsto',
        'fin_previsto',
        'entregado_en',
        'devuelto_en',
        'condicion_salida',
        'condicion_retorno',
        'estado',
    ];

    protected $casts = [
        'inicio_previsto' => 'datetime',
        'fin_previsto' => 'datetime',
        'entregado_en' => 'datetime',
        'devuelto_en' => 'datetime',
        'condicion_salida' => 'string',
        'condicion_retorno' => 'string',
        'estado' => 'string',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id', 'codigo');
    }

    public function activo(): BelongsTo
    {
        return $this->belongsTo(Activo::class, 'activo_codigo', 'codigo');
    }

    
}