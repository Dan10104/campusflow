<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\Reserva;
use App\Models\Activo;
use App\Models\Aula;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Services\PredictionService; // Importar

class DashboardController extends Controller
{
    public function __construct(
        private PredictionService $predictionService
    ) {}

    public function index()
    {
        // 1. KPIs Principales
        $totalReservasMes = Reserva::whereMonth('inicio', now()->month)->count();
        $activosPrestados = Activo::where('estado', 'prestado')->count();
        $aulasMantenimiento = Aula::where('estado', 'mantenimiento')->count();

        // [NUEVO] Predicciones (CU-14)
        $predicciones = $this->predictionService->getHighDemandPrediction();

        // 2. Gráfico: Reservas por Estado (Confirmada, Pendiente, Cancelada, etc.)
        $statsEstado = Reserva::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        // 3. Gráfico: Aulas más solicitadas (Top 5)
        $aulasTop = Reserva::select('aula_codigo', DB::raw('count(*) as total'))
            ->with('aula:codigo,tipo,modulo_codigo') // Asumiendo relación
            ->groupBy('aula_codigo')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function($item) {
                // Ajusta según cómo accedas al nombre/código del aula
                return [
                    'nombre' => 'Aula ' . $item->aula_codigo . ' (' . $item->aula->tipo . ')',
                    'total' => $item->total
                ];
            });

        // 4. Lista: Próximas Reservas (Para hoy/mañana)
        $proximasReservas = Reserva::with(['usuario', 'aula.modulo'])
            ->where('inicio', '>=', now())
            ->orderBy('inicio', 'asc')
            ->limit(5)
            ->get();

        // 5. Lista: Activos en Mantenimiento
        $activosMantenimiento = Activo::where('estado', 'mantenimiento')
            ->with('tipoActivo')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'kpis' => [
                'reservas_mes' => $totalReservasMes,
                'activos_prestados' => $activosPrestados,
                'aulas_mantenimiento' => $aulasMantenimiento
            ],
            'charts' => [
                'estados' => $statsEstado,
                'aulas_top' => $aulasTop
            ],
            'listas' => [
                'proximas_reservas' => $proximasReservas,
                'activos_mantenimiento' => $activosMantenimiento,
                'predicciones' => $predicciones // [NUEVO]
            ]
        ]);
    }

    public function exportar(Request $request, $tipo)
    {
        $fecha = now()->format('d/m/Y H:i');
        
        // Datos para el reporte (Podrías filtrar por fechas si quisieras)
        $reservas = Reserva::with(['usuario', 'aula'])->orderBy('inicio', 'desc')->take(50)->get();
        $activos = Activo::where('estado', '!=', 'disponible')->get();

        if ($tipo === 'pdf') {
            $pdf = Pdf::loadView('reports.dashboard_pdf', compact('reservas', 'activos', 'fecha'));
            return $pdf->download('reporte_gestion_uagrm.pdf');
        } 
        
        if ($tipo === 'csv') {
            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=reporte_reservas.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $callback = function() use ($reservas) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID Reserva', 'Usuario', 'Aula', 'Inicio', 'Fin', 'Estado']);

                foreach ($reservas as $reserva) {
                    fputcsv($file, [
                        $reserva->id,
                        $reserva->usuario->nombre ?? 'N/A',
                        $reserva->aula_codigo,
                        $reserva->inicio,
                        $reserva->fin,
                        $reserva->estado
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }
}