<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoliticaLiberacion extends Model
{
    protected $table = 'politicas_liberacion';
    protected $primaryKey = 'codigo';
    public $timestamps = false;
    
    protected $fillable = [
        'nivel',
        'umbral_minutos',
        'ventana_histeresis_seg',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'creado_en' => 'datetime',
    ];
}