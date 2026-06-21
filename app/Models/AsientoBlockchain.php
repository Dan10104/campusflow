<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsientoBlockchain extends Model
{
    protected $table = 'asientos_blockchain';
    protected $primaryKey = 'codigo';
    public $timestamps = false;
    
    protected $fillable = [
        'reserva_id',
        'tx_id',
        'hash',
        'bloque',
        'red',
    ];

    protected $casts = [
        'creado_en' => 'datetime',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }
}