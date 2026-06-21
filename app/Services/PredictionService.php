<?php

namespace App\Services;

use Carbon\Carbon;

class PredictionService
{
    /**
     * Predice qué aulas tendrán alta demanda mañana.
     * Retorna una lista de aulas y su probabilidad de ocupación.
     */
    public function getHighDemandPrediction(): array
    {
        // Lógica Heurística Simple (Prototipo)
        // En producción, esto usaría ML o regresión histórica.
        
        $tomorrow = Carbon::tomorrow();
        $dayOfWeek = $tomorrow->dayOfWeek; // 0 (Sun) - 6 (Sat)
        
        // Si es fin de semana, baja demanda
        // if ($dayOfWeek === 0 || $dayOfWeek === 6) {
        //    return [];
        // }

        // Simulación: Aulas de planta baja (Modulo 224) siempre llenas
        return [
            [
                'aula' => '224-01',
                'nivel_demanda' => 'Critico',
                'probabilidad' => 95,
                'hora_pico' => '07:00 - 10:00'
            ],
            [
                'aula' => '224-02',
                'nivel_demanda' => 'Alto',
                'probabilidad' => 85,
                'hora_pico' => '18:00 - 21:00'
            ],
            [
                'aula' => 'LAB-1',
                'nivel_demanda' => 'Medio',
                'probabilidad' => 60,
                'hora_pico' => '14:00 - 16:00'
            ]
        ];
    }
}
