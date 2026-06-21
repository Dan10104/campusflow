<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'codigo';
    public $timestamps = false;
    
    protected $fillable = [
        'usuario_id',
        'tipo',
        'canal',
        'contenido',
        'estado_envio',
        'enviado_en',
        'leida',
    ];

    protected $casts = [
        'tipo' => 'string',
        'canal' => 'string',
        'estado_envio' => 'string',
        'enviado_en' => 'datetime',
        'leida' => 'boolean',
        'creado_en' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}