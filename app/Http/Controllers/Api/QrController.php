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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QrController extends Controller
{
    public function checkinAula(ScanAulaRequest $request): JsonResponse
    {
        $user = $request->user();
        $qrCode = $request->input('qr_code_id');
        $now = Carbon::now();

        $aula = Aula::where('qr_code', $qrCode)->first();

        if (!$aula) {
            return response()->json([
                'success' => false,
                'message' => 'Código QR no reconocido (Aula no encontrada).',
                'data' => null,
            ], 404);
        }

        return DB::transaction(function () use ($user, $aula, $now) {
            $reservaActiva = Reserva::where('usuario_id', $user->codigo)
                ->where('aula_codigo', $aula->codigo)
                ->where('estado', 'aprobada')
                ->where('inicio', '<=', $now->copy()->addMinutes(15))
                ->where('fin', '>=', $now)
                ->lockForUpdate()
                ->first();

            if (!$reservaActiva) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes una reserva activa o proxima para realizar check-in en este momento.',
                    'data' => null,
                ], 403);
            }

            $yaHizoCheckin = CheckinReserva::where('reserva_id', $reservaActiva->id)
                ->where('tipo', 'checkin')
                ->exists();

            if ($yaHizoCheckin) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ya has realizado el check-in previamente para esta reserva.',
                    'data' => null,
                ], 200);
            }

            $checkin = CheckinReserva::create([
                'reserva_id' => $reservaActiva->id,
                'tipo' => 'checkin',
                'registrado_en' => $now,
                'origen' => 'qr',
            ]);

            $reservaActiva->update(['estado' => 'en_uso']);

            return response()->json([
                'success' => true,
                'message' => 'Check-in realizado con exito.',
                'data' => [
                    'reserva_id' => $reservaActiva->id,
                    'checkin' => $checkin,
                ],
            ], 200);
        });
    }

    public function scanActivo(ScanActivoRequest $request): JsonResponse
    {
        $user = $request->user();
        $qrCode = $request->input('qr_code_id');
        $accion = $request->input('accion');
        $now = Carbon::now();

        try {
            return DB::transaction(function () use ($user, $qrCode, $accion, $now) {
                $activo = Activo::where('qr_code', $qrCode)
                    ->lockForUpdate()
                    ->first();

                if (!$activo) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Código QR no reconocido (Activo no encontrado).',
                        'data' => null,
                    ], 404);
                }

                if ($accion === 'prestamo') {
                    return $this->registrarPrestamoPorQr($user->codigo, $activo, $now);
                }

                if ($accion === 'devolucion') {
                    return $this->registrarDevolucionPorQr($user->codigo, $activo, $now);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Accion no valida.',
                    'data' => null,
                ], 400);
            });
        } catch (\Throwable $e) {
            Log::error('Error procesando escaneo QR de activo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'No fue posible procesar el escaneo del activo.',
                'data' => null,
            ], 500);
        }
    }

    private function registrarPrestamoPorQr(int $usuarioCodigo, Activo $activo, Carbon $now): JsonResponse
    {
        if ($activo->estado !== 'disponible') {
            return response()->json([
                'success' => false,
                'message' => 'El activo no se encuentra disponible actualmente.',
                'data' => ['estado_actual' => $activo->estado],
            ], 403);
        }

        $tienePrestamoFisicoActivo = ReservaActivos::where('activo_codigo', $activo->codigo)
            ->whereIn('estado', ['entregado', 'vencido'])
            ->exists();

        if ($tienePrestamoFisicoActivo) {
            return response()->json([
                'success' => false,
                'message' => 'El activo ya tiene un préstamo físico activo.',
                'data' => null,
            ], 409);
        }

        Log::info('Reserva activa para el activo: ' . $activo->codigo);
        Log::info('inicio_previsto: ' . $now->copy()->addMinutes(15));

        $reserva = ReservaActivos::where('activo_codigo', $activo->codigo)
            ->where('usuario_id', $usuarioCodigo)
            ->where('estado', 'aprobado')
            ->where('inicio_previsto', '<=', $now->copy()->addMinutes(15))
            ->lockForUpdate()
            ->first();

        if (!$reserva) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes una reserva activa o aprobada para recoger este equipo.',
                'data' => null,
            ], 403);
        }

        $reserva->update([
            'estado' => 'entregado',
            'entregado_en' => $now,
        ]);

        $activo->update(['estado' => 'prestado']);

        return response()->json([
            'success' => true,
            'message' => 'Prestamo de activo registrado con exito.',
            'data' => ['activo' => $activo->fresh(), 'reserva' => $reserva->fresh()],
        ], 200);
    }

    private function registrarDevolucionPorQr(int $usuarioCodigo, Activo $activo, Carbon $now): JsonResponse
    {
        $reserva = ReservaActivos::where('activo_codigo', $activo->codigo)
            ->where('usuario_id', $usuarioCodigo)
            ->whereIn('estado', ['entregado', 'vencido'])
            ->lockForUpdate()
            ->first();

        if (!$reserva) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes este activo en préstamo actualmente bajo tu usuario.',
                'data' => null,
            ], 403);
        }

        $reserva->update([
            'estado' => 'devuelto',
            'devuelto_en' => $now,
        ]);

        $tieneOtroPrestamoFisicoActivo = ReservaActivos::where('activo_codigo', $activo->codigo)
            ->where('id', '!=', $reserva->id)
            ->whereIn('estado', ['entregado', 'vencido'])
            ->exists();

        if (!$tieneOtroPrestamoFisicoActivo) {
            $activo->update(['estado' => 'disponible']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Devolucion de activo registrada con exito.',
            'data' => ['activo' => $activo->fresh(), 'reserva' => $reserva->fresh()],
        ], 200);
    }
}
