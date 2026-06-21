<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Listado paginado de aulas con su estado dinámico actual
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $pabellon = $request->query('pabellon'); // o modulo_codigo

        $query = Aula::with(['modulo', 'caracteristicas']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('tipo', 'like', "%{$search}%");
            });
        }

        if ($pabellon) {
            $query->where('modulo_codigo', $pabellon);
        }

        // Recuperar en paginación
        $paginator = $query->paginate(15);

        // Hora actual para calcular el estado dinámico
        $now = Carbon::now();

        // Mapear los resultados para incluir el estado calculado
        $paginator->getCollection()->transform(function ($aula) use ($now) {
            
            // Verificar si hay una reserva aprobada que esté activa justo AHORA en esta aula.
            // Esto asume que tenemos cargadas las reservas, o hacemos una raw query.
            // Para ser óptimo sin cargar todas las reservas de la historia:
            $reservaVigente = $aula->reservas()
                ->where('estado', 'aprobada')
                ->where('inicio', '<=', $now)
                ->where('fin', '>=', $now)
                ->first();

            $bloqueoVigente = $aula->bloqueos()
                ->where('inicio', '<=', $now)
                ->where('fin', '>=', $now)
                ->first();

            $estadoDinamico = 'Disponible';
            if ($bloqueoVigente) {
                $estadoDinamico = 'Bloqueada';
            } elseif ($reservaVigente) {
                $estadoDinamico = 'Ocupada';
            }

            // Sobreescribir el estado para la vista API
            $aula->estado_dinamico = $estadoDinamico;
            
            // Adjuntamos info de la reserva si está ocupada
            if ($reservaVigente) {
                $aula->reserva_actual = [
                    'id' => $reservaVigente->id,
                    'proposito' => $reservaVigente->proposito,
                    'fin' => $reservaVigente->fin->toDateTimeString(),
                ];
            }

            return $aula;
        });

        return response()->json([
            'success' => true,
            'message' => 'Lista de aulas consultada correctamente',
            'data' => $paginator
        ], 200);
    }
}
