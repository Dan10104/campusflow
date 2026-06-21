<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Aula;
use App\Services\IoTService;

class IoTController extends Controller
{
    public function __construct(
        private IoTService $iotService
    ) {}

    /**
     * Display the IoT Dashboard.
     */
    public function index(): Response
    {
        // Obtener aulas monitoreables (en este prototipo, todas)
        $aulas = Aula::with('modulo')->get()->map(function ($aula) {
            return [
                'codigo' => $aula->codigo,
                'nombre' => $aula->modulo->nombre . ' - ' . $aula->tipo,
                // Consultar estado en tiempo real (Simulado)
                'sensor_status' => $this->iotService->getSensorStatus($aula->codigo),
                'movimiento' => $this->iotService->detectarMovimiento($aula->codigo),
                'temperatura' => $this->iotService->getTemperatura($aula->codigo),
                // Datos simulados extra para el dashboard
                'humedad' => rand(40, 60),
                'ultima_actualizacion' => now()->toTimeString()
            ];
        });

        return Inertia::render('IoT/Dashboard', [
            'sensores' => $aulas
        ]);
    }
}
