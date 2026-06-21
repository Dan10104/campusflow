<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Servicio Mocj para Simulación de IoT
 * En producción, esto se conectaría a un Broker MQTT o API de Gateway IoT
 */
class IoTService
{
    /**
     * Verifica si el Gateway del aula está conectado
     */
    public function getSensorStatus(string $aulaCodigo): string
    {
        // SIMULACIÓN: Return 'online' 90% del tiempo
        return (rand(1, 10) > 1) ? 'online' : 'offline';
    }

    /**
     * Detecta si hay movimiento en el aula en los últimos X minutos
     * @return bool true (Hay gente) | false (Aula vacía)
     */
    public function detectarMovimiento(string $aulaCodigo): bool
    {
        // SIMULACIÓN: 
        // Si el código del aula termina en '00', simulamos que está vacía (para pruebas)
        if (str_ends_with($aulaCodigo, '00')) {
            Log::info("IoT: Aula $aulaCodigo detectada VACÍA (Simulación).");
            return false;
        }

        // Por defecto, asumimos que hay gente
        return true;
    }

    /**
     * Obtiene temperatura actual
     */
    public function getTemperatura(string $aulaCodigo): float
    {
        return rand(200, 280) / 10; // 20.0 a 28.0 grados
    }
}
