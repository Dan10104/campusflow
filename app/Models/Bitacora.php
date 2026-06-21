<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    protected $primaryKey = 'codigo';
    public $timestamps = false;
    
    protected $fillable = [
        'entidad',
        'accion',
        'usuario_id',
        'detalle_json',
        'ip',
    ];

    protected $casts = [
        'detalle_json' => 'array',
        'creado_en' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}