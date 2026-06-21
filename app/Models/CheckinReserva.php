<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckinReserva extends Model
{
    protected $table = 'checkins_reserva';
    
    protected $fillable = [
        'reserva_id',
        'tipo',
        'registrado_en',
        'origen',
    ];

    protected $casts = [
        'registrado_en' => 'datetime',
        'tipo' => 'string',
        'origen' => 'string',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }
}