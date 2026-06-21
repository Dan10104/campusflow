<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facultad extends Model
{
    protected $table = 'facultades';
    protected $primaryKey = 'codigo';
    
    protected $fillable = [
        'nombre',
        'sigla',
    ];

    public function modulos(): HasMany
    {
        return $this->hasMany(Modulo::class, 'facultad_codigo', 'codigo');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'facultad_codigo', 'codigo');
    }
}
