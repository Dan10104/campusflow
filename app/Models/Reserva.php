<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reserva extends Model
{
    protected $table = 'reservas';
    
    protected $fillable = [
        'usuario_id',
        'aula_codigo',
        'inicio',
        'fin',
        'proposito',
        'asistentes_estimados',
        'estado',
        'qr_code',
        'creado_por',
        'cancelado_por',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime',
        'estado' => 'string',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class, 'aula_codigo', 'codigo');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function canceladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(CheckinReserva::class, 'reserva_id');
    }

    public function asientoBlockchain(): HasOne
    {
        return $this->hasOne(AsientoBlockchain::class, 'reserva_id');
    }
}