<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReservationRequest;
use App\Models\Aula;
use App\Models\Reserva;
use App\Models\ReservaActivos;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    /**
     * Crear una reserva para Aula o Activo.
     * Incluye validación anti-solapamiento (overlap).
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        $user = $request->user();
        $type = $request->input('type');
        $id = $request->input('id');
        
        if ($type === 'classroom') {
            $fecha = $request->input('fecha');
            // Convertimos a Carbon DateTime usando zona horaria correcta
            $inicio = Carbon::createFromFormat('Y-m-d H:i', "{$fecha} {$request->hora_inicio}", 'America/La_Paz')->setTimezone('UTC');
            $fin = Carbon::createFromFormat('Y-m-d H:i', "{$fecha} {$request->hora_fin}", 'America/La_Paz')->setTimezone('UTC');

            // Verificar solapamiento (Reserva de aula)
            $isOverlapped = Reserva::where('aula_codigo', $id)
                ->whereIn('estado', ['pendiente', 'aprobada', 'en_curso'])
                ->where(function ($query) use ($inicio, $fin) {
                    $query->where('inicio', '<', $fin)
                          ->where('fin', '>', $inicio);
                })->exists();

            if ($isOverlapped) {
                return response()->json([
                    'success' => false,
                    'message' => 'El aula ya se encuentra reservada o pendiente en ese horario.',
                    'data' => null
                ], 422);
            }

            // Crear la reserva
            $reserva = Reserva::create([
                'usuario_id' => $user->codigo,
                'aula_codigo' => $id,
                'inicio' => $inicio,
                'fin' => $fin,
                'proposito' => $request->input('proposito', 'Sin propósito especificado'),
                'asistentes_estimados' => $request->input('asistentes_estimados'),
                'estado' => 'aprobada', // Podría ser pendiente según regla de negocio
                'qr_code' => Str::random(10), // Random provisorio
                'creado_por' => $user->codigo,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reserva de aula creada exitosamente.',
                'data' => $reserva
            ], 201);
            
        } elseif ($type === 'asset') {
            // En activos recibimos fecha_inicio y fecha_fin separadas (con su hora incluida)
            $inicio = Carbon::createFromFormat('Y-m-d H:i', $request->input('fecha_inicio'), 'America/La_Paz')->setTimezone('UTC');
            $fin = Carbon::createFromFormat('Y-m-d H:i', $request->input('fecha_fin'), 'America/La_Paz')->setTimezone('UTC');
            
            // Verificar solapamiento (Reserva de activo)
            $isOverlapped = ReservaActivos::where('activo_codigo', $id)
                ->whereIn('estado', ['solicitada', 'aprobada', 'entregado'])
                ->where(function ($query) use ($inicio, $fin) {
                    $query->where('inicio_previsto', '<', $fin)
                          ->where('fin_previsto', '>', $inicio);
                })->exists();

            if ($isOverlapped) {
                return response()->json([
                    'success' => false,
                    'message' => 'El activo ya se encuentra reservado en ese horario.',
                    'data' => null
                ], 422);
            }

            // Crear la reserva activo
            $reservaActivo = ReservaActivos::create([
                'usuario_id' => $user->codigo,
                'activo_codigo' => $id,
                'inicio_previsto' => $inicio,
                'fin_previsto' => $fin,
                'estado' => 'aprobado',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reserva de activo creada exitosamente.',
                'data' => $reservaActivo
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tipo de reserva no válido.',
            'data' => null
        ], 422);
    }

    /**
     * Endpoint para consultar mi calendario (Reservas pasadas y futuras)
     */
    public function myCalendar(): JsonResponse
    {
        $user = request()->user();

        // Obtener Aulas
        $reservasAulas = Reserva::with('aula.modulo')
            ->where('usuario_id', $user->codigo)
            ->orderBy('inicio', 'asc')
            ->get()
            ->map(function ($reserva) {
                return [
                    'id' => $reserva->id,
                    'type' => 'classroom',
                    'title' => "Aula: {$reserva->aula_codigo}",
                    'location' => $reserva->aula->modulo->nombre ?? 'N/A',
                    'start' => $reserva->inicio->toDateTimeString(),
                    'end' => $reserva->fin->toDateTimeString(),
                    'status' => $reserva->estado,
                ];
            });

        // Obtener Activos
        $reservasActivos = ReservaActivos::with('activo.tipoActivo')
            ->where('usuario_id', $user->codigo)
            ->orderBy('inicio_previsto', 'asc')
            ->get()
            ->map(function ($reserva) {
                return [
                    'id' => $reserva->id,
                    'type' => 'asset',
                    'title' => "Activo: " . ($reserva->activo->descripcion ?? $reserva->activo_codigo),
                    'start' => $reserva->inicio_previsto->toDateTimeString(),
                    'end' => $reserva->fin_previsto->toDateTimeString(),
                    'status' => $reserva->estado,
                ];
            });

        // Unificar resultados para calendario
        $allReservations = $reservasAulas->concat($reservasActivos)->sortByDesc('start')->values();

        return response()->json([
            'success' => true,
            'message' => 'Calendario obtenido correctamente',
            'data' => $allReservations
        ], 200);
    }
}
