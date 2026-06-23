<?php

namespace App\Http\Controllers;

use App\Exports\DashboardExport;
use App\Models\Activo;
use App\Models\Aula;
use App\Models\Reserva;
use App\Services\PredictionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private PredictionService $predictionService
    ) {}

    public function index()
    {
        return Inertia::render('Dashboard', $this->obtenerDatosDashboard());
    }

    private function obtenerDatosDashboard(): array
    {
        // 1. KPIs principales que ya muestra el Dashboard.
        $totalReservasMes = Reserva::whereMonth('inicio', now()->month)->count();
        $activosPrestados = Activo::where('estado', 'prestado')->count();
        $aulasMantenimiento = Aula::where('estado', 'mantenimiento')->count();

        // 2. Predicciones demostrativas (CU-14).
        $predicciones = $this->predictionService->getHighDemandPrediction();

        // 3. Gráfico: reservas por estado.
        $statsEstado = Reserva::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        // 4. Gráfico: aulas más solicitadas.
        $aulasTop = Reserva::select('aula_codigo', DB::raw('count(*) as total'))
            ->with('aula:codigo,tipo,modulo_codigo')
            ->groupBy('aula_codigo')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $tipo = $item->aula?->tipo ?: 'aula';

                return [
                    'nombre' => 'Aula ' . $item->aula_codigo . ' (' . $tipo . ')',
                    'total' => $item->total,
                ];
            });

        // 5. Lista: próximas reservas.
        $proximasReservas = Reserva::with(['usuario', 'aula.modulo'])
            ->where('inicio', '>=', now())
            ->orderBy('inicio', 'asc')
            ->limit(5)
            ->get();

        // 6. Lista: activos en mantenimiento.
        $activosMantenimiento = Activo::where('estado', 'mantenimiento')
            ->with('tipoActivo')
            ->limit(5)
            ->get();

        return [
            'kpis' => [
                'reservas_mes' => $totalReservasMes,
                'activos_prestados' => $activosPrestados,
                'aulas_mantenimiento' => $aulasMantenimiento,
            ],
            'charts' => [
                'estados' => $statsEstado,
                'aulas_top' => $aulasTop,
            ],
            'listas' => [
                'proximas_reservas' => $proximasReservas,
                'activos_mantenimiento' => $activosMantenimiento,
                'predicciones' => $predicciones,
            ],
        ];
    }

    public function exportar(Request $request, $tipo)
    {
        if (! in_array($tipo, ['pdf', 'excel'], true)) {
            abort(404);
        }

        try {
            $datos = $this->obtenerDatosDashboard();
            $fechaGeneracion = now();
            $nombreBase = 'reporte-dashboard-campusflow-' . $fechaGeneracion->format('Y-m-d');

            if ($tipo === 'pdf') {
                $pdf = Pdf::loadView('reports.dashboard_pdf', [
                    ...$datos,
                    'fechaGeneracion' => $fechaGeneracion,
                ])->setPaper('a4', 'landscape');

                return $pdf->download($nombreBase . '.pdf');
            }

            if ($tipo === 'excel') {
                return (new DashboardExport($datos, $fechaGeneracion))
                    ->download($nombreBase . '.xlsx');
            }
        } catch (\Throwable $exception) {
            Log::error('Error al exportar Dashboard.', [
                'formato' => $tipo,
                'message' => $exception->getMessage(),
            ]);

            return response('No se pudo generar el reporte del Dashboard.', 500);
        }
    }
}
