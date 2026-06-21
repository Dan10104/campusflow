<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activo extends Model
{
    protected $table = 'activos';
    protected $primaryKey = 'codigo';
    
    protected $fillable = [
        'descripcion',
        'tipo_activo_id',
        'estado',
        'ubicacion_actual',
        'qr_code',
        'marca',
        'modelo',
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    public function tipoActivo(): BelongsTo
    {
        return $this->belongsTo(TipoActivo::class, 'tipo_activo_id');
    }

    public function reservasActivos(): HasMany
    {
        return $this->hasMany(ReservaActivos::class, 'activo_codigo', 'codigo');
    }
}