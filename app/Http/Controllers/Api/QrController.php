<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ScanActivoRequest;
use App\Http\Requests\Api\ScanAulaRequest;
use App\Models\Activo;
use App\Models\Aula;
use App\Models\CheckinReserva;
use App\Models\Reserva;
use App\Models\ReservaActivos;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class QrController extends Controller
{
    /**
     * Endpoint para realizar Check-in en un aula vía código QR.
     * Solo es válido si hay una reserva aprobada para el usuario en ±15 mins del NOW.
     */
    public function checkinAula(ScanAulaRequest $request): JsonResponse
    {
        $user = $request->user();
        $qrCode = $request->input('qr_code_id');
        $now = Carbon::now();

        // 1. Buscar el aula por el QR
        $aula = Aula::where('qr_code', $qrCode)->first();

        if (!$aula) {
            return response()->json([
                'success' => false,
                'message' => 'Código QR no reconocido (Aula no encontrada).',
                'data' => null
            ], 404);
        }

        // 2. Buscar reserva activa del usuario para esta aula (rango ± 15 mins del inicio previsto)
        // Ejemplo de lógica: Permitir check-in hasta 15 min antes del inicio, y durante todo el tiempo de la reserva.
        $reservaActiva = Reserva::where('usuario_id', $user->codigo)
            ->where('aula_codigo', $aula->codigo)
            ->where('estado', 'aprobada')
            ->where('inicio', '<=', $now->copy()->addMinutes(15))
            ->where('fin', '>=', $now)
            ->first();

        if (!$reservaActiva) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes una reserva activa o próxima para realizar check-in en este momento.',
                'data' => null
            ], 403);
        }

        // 3. Ya se validó la reserva, verificamos si ya hizo checkin
        $yaHizoCheckin = CheckinReserva::where('reserva_id', $reservaActiva->id)
            ->where('tipo', 'ingreso')
            ->exists();

        if ($yaHizoCheckin) {
            return response()->json([
                'success' => true,
                'message' => 'Ya has realizado el check-in previamente para esta reserva.',
                'data' => null
            ], 200);
        }

        // 4. Crear el log de Check-in y actualizar estado de reserva si corresponde.
        $checkin = CheckinReserva::create([
            'reserva_id' => $reservaActiva->id,
            'qr_code_escanedo' => $qrCode,
            'timestamp' => $now,
            'metodo' => 'qr',
            'tipo' => 'ingreso',
        ]);

        $reservaActiva->update(['estado' => 'en_uso']);

        return response()->json([
            'success' => true,
            'message' => 'Check-in realizado con éxito.',
            'data' => [
                'reserva_id' => $reservaActiva->id,
                'checkin' => $checkin
            ]
        ], 200);
    }

    /**
     * Endpoint para procesar escaneo de activos físicos (prestar o devolver).
     */
    public function scanActivo(ScanActivoRequest $request): JsonResponse
    {
        $user = $request->user();
        $qrCode = $request->input('qr_code_id');
        $accion = $request->input('accion');
        $now = Carbon::now();

        // 1. Buscar Activo
        $activo = Activo::where('qr_code', $qrCode)->first();

        if (!$activo) {
            return response()->json([
                'success' => false,
                'message' => 'Código QR no reconocido (Activo no encontrado).',
                'data' => null
            ], 404);
        }

        // 2. Ejecutar Lógicas dependiendo de Acción
        if ($accion === 'prestamo') {
            if ($activo->estado !== 'Disponible') {
                return response()->json([
                    'success' => false,
                    'message' => 'El activo no se encuentra disponible actualmente.',
                    'data' => ['estado_actual' => $activo->estado]
                ], 403);
            }
            Log::info("Reserva activa para el activo: " . $activo->codigo);
            Log::info("inicio_previsto: " . $now->copy()->addMinutes(15));
            // Buscar si tiene reserva aprobada para este activo
            $reserva = ReservaActivos::where('activo_codigo', $activo->codigo)
                ->where('usuario_id', $user->codigo)
                ->where('estado', 'aprobado')
                ->where('inicio_previsto', '<=', $now->copy()->addMinutes(15))
                ->first();

            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes una reserva activa o aprobada para recoger este equipo.',
                    'data' => null
                ], 403);
            }

            // Actualizar a entregado
            $reserva->update([
                'estado' => 'entregado',
                'entregado_en' => $now
            ]);

            $activo->update(['estado' => 'Prestado']);

            return response()->json([
                'success' => true,
                'message' => 'Préstamo de activo registrado con éxito.',
                'data' => ['activo' => $activo, 'reserva' => $reserva]
            ], 200);
            
        } elseif ($accion === 'devolucion') {
            
            // Buscar la reserva "entregado" asociada a este user y objeto
            $reserva = ReservaActivos::where('activo_codigo', $activo->codigo)
                ->where('usuario_id', $user->codigo)
                ->where('estado', 'entregado')
                ->first();

            if (!$reserva) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes este activo en préstamo actualmente bajo tu usuario.',
                    'data' => null
                ], 403);
            }

            // Finalizar uso
            $reserva->update([
                'estado' => 'devuelto',
                'devuelto_en' => $now
            ]);

            $activo->update(['estado' => 'Disponible']);

            return response()->json([
                'success' => true,
                'message' => 'Devolución de activo registrada con éxito.',
                'data' => ['activo' => $activo, 'reserva' => $reserva]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Acción no válida.',
            'data' => null
        ], 400);
    }
}
