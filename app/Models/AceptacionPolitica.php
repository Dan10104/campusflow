<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AceptacionPolitica extends Model
{
    public const VERSION_TERMINOS = '1.0';
    public const VERSION_PRIVACIDAD = '1.0';

    protected $table = 'aceptaciones_politicas';

    protected $fillable = [
        'usuario_id',
        'version_terminos',
        'version_privacidad',
        'aceptado_en',
        'direccion_ip',
        'user_agent',
        'identificador_sesion',
    ];

    protected function casts(): array
    {
        return [
            'aceptado_en' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id', 'codigo');
    }

    public static function hashSessionIdentifier(string $identifier): string
    {
        return hash('sha256', $identifier);
    }
}
