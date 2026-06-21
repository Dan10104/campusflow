<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloqueoAula extends Model
{
    protected $table = 'bloqueos_aula';
    protected $primaryKey = 'codigo';
    
    protected $fillable = [
        'aula_codigo',
        'motivo',
        'inicio',
        'fin',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime',
    ];

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class, 'aula_codigo', 'codigo');
    }
}
