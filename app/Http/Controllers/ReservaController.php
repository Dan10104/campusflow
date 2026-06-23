<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use App\Models\Reserva;
use App\Models\Aula;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\CheckinReserva;
use App\Services\BlockchainService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Importante para ver errores reales
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\FirebaseService;

class ReservaController extends Controller
{
    public function index(Request $request): Response
    {
        $usuario = $request->user();
        
        $reservas = Reserva::with([
            'aula.modulo.facultad',
            'checkins' => function($query) {
                $query->orderBy('registrado_en', 'desc');
            }
        ])
        // CORRECCIÓN: Usar 'codigo' en lugar de 'id'
        ->where('usuario_id', $usuario->codigo) 
        ->orderBy('inicio', 'desc')
        ->get();
        
        return Inertia::render('Reservas/MisReservas', [
            'reservas' => $reservas,
            'usuario' => $usuario
        ]);
    }
    
    public function crear(Request $request)
    {
        $aulaId = $request->input('aula');
        
        if (!$aulaId) {
            // Si no hay aula seleccionada, redirigir a la lista de aulas
            return redirect()->route('aulas.disponibles')
                ->with('warning', 'Por favor seleccione un aula para reservar.');
        }

        $aula = Aula::with(['modulo.facultad', 'caracteristicas'])
            ->where('codigo', $aulaId)
            ->first();

        if (!$aula) {
             return redirect()->route('aulas.disponibles')
                ->withErrors(['error' => 'El aula seleccionada no existe.']);
        }
        
        return Inertia::render('Reservas/Crear', [
            'aula' => $aula,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'aula_codigo' => 'required|exists:aulas,codigo',
            'inicio' => 'required|date|after:now',
            'fin' => 'required|date|after:inicio',
            'proposito' => 'required|string|max:500',
            'asistentes_estimados' => 'nullable|integer|min:1'
        ]);
        
        $usuario = $request->user();
        
        // Verificar disponibilidad
        $conflictos = Reserva::where('aula_codigo', $validated['aula_codigo'])
            ->whereIn('estado', ['confirmada', 'aprobada', 'en_uso'])
            ->where(function($q) use ($validated) {
                $q->where('inicio', '<', $validated['fin'])
                  ->where('fin', '>', $validated['inicio']);
            })
            ->exists();
        
        if ($conflictos) {
            throw ValidationException::withMessages([
                'conflicto' => 'El aula no está disponible en ese horario seleccionado.'
            ]);
        }
        
        DB::beginTransaction();
        
        try {
            // CORRECCIÓN PRINCIPAL: Usar $usuario->codigo
            $reserva = Reserva::create([
                'usuario_id' => $usuario->codigo, // <--- Aquí estaba el error
                'aula_codigo' => $validated['aula_codigo'],
                'inicio' => $validated['inicio'],
                'fin' => $validated['fin'],
                'proposito' => $validated['proposito'],
                'asistentes_estimados' => $validated['asistentes_estimados'],
                'estado' => 'pendiente', // [MODIFICADO] Requiere aprobación admin
                'creado_por' => $usuario->codigo,
                'qr_code' => 'pending'
            ]);

            // Actualizar QR con URL firmada real
            $signedUrl = \Illuminate\Support\Facades\URL::signedRoute('reservas.checkin', ['id' => $reserva->id]);
            $reserva->update(['qr_code' => $signedUrl]);
            
            DB::commit();

            // Notificar a los administradores sobre la nueva reserva
            $firebase = new FirebaseService();
            $admins = \App\Models\User::whereHas('roles', function($q) {
                // Admin general (1) o encargado de aulas (4?) 
                // Segun User.php esAdmin usa [1, 4]
                $q->whereIn('rol_id', [1, 4]);
            })->whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

            $firebase->sendToMultiple(
                $admins,
                "Nueva Reserva de Aula",
                "El usuario {$usuario->nombre} ha reservado el aula: " . ($reserva->aula->nombre ?? $reserva->aula_codigo),
                ['reserva_id' => $reserva->id, 'tipo' => 'nueva_reserva']
            );

            try {
        $blockchain = new BlockchainService();
            $blockchain->registrarAuditoria($reserva->id, [
                'usuario' => $usuario->codigo,
                'aula' => $validated['aula_codigo'],
                'fecha' => now()->toIso8601String(),
                'accion' => 'CREAR_RESERVA'
            ]);
        } catch (\Exception $e) {
            Log::warning("No se pudo auditar en blockchain: " . $e->getMessage());
        }
            
            return redirect()->route('reservas.index')
                ->with('success', 'Reserva creada exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            // Loguear el error real para que sepas qué pasó
            Log::error("Error creando reserva: " . $e->getMessage());
            
            throw ValidationException::withMessages([
                'error' => 'Ocurrió un error inesperado al procesar la reserva.'
            ]);
        }
    }
    
    public function show(int $id): Response
    {
        $reserva = Reserva::with([
            'aula.modulo.facultad',
            'usuario',
            'checkins',
            'asientoBlockchain'
        ])->findOrFail($id);
        
        // CORRECCIÓN: Usar codigo
        if ($reserva->usuario_id !== Auth::user()->codigo) {
            abort(403);
        }
        
        return Inertia::render('Reservas/Show', [
            'reserva' => $reserva
        ]);
    }
    
    public function checkin(Request $request, int $id)
    {
        // 1. Validar Firma Digital del QR (Seguridad CU-05)
        if (!$request->hasValidSignature()) {
            return response()->json(['message' => 'El código QR no es válido.'], 403);
        }

        $reserva = Reserva::findOrFail($id);
        
        $usuario = $request->user();
        if (!$usuario->esAdmin()) {
             return response()->json(['message' => 'No autorizado'], 403);
        }
        
        if (!in_array($reserva->estado, ['confirmada', 'aprobada'])) {
            return response()->json(['message' => 'Estado inválido'], 400);
        }
        
        // Lógica de ventana de tiempo (sin cambios)
        $ahora = now();
        $ventanaInicio = Carbon::parse($reserva->inicio)->subMinutes(15);
        $ventanaFin = Carbon::parse($reserva->fin);
        
        if ($ahora < $ventanaInicio || $ahora > $ventanaFin) {
            return response()->json(['message' => 'La reserva no está dentro del horario permitido para check-in.'], 400);
        }
        
        // Evitar doble checkin
        $tieneCheckin = CheckinReserva::where('reserva_id', $id)
            ->where('tipo', 'checkin')
            ->exists();
        
        if ($tieneCheckin) {
            return response()->json(['message' => 'El check-in ya fue registrado.'], 400);
        }
        
        DB::beginTransaction();
        try {
            CheckinReserva::create([
                'reserva_id' => $id,
                'tipo' => 'checkin',
                'registrado_en' => now(),
                'origen' => 'qr'
            ]);
            
            $reserva->update(['estado' => 'en_uso']);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Check-in registrado correctamente.',
                'reserva' => $reserva->fresh(['checkins'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    public function escanear()
    {
        return Inertia::render('Reservas/Escanear');
    }

    public function checkout(Request $request, int $id)
    {
        $usuario = $request->user();

        $resultado = DB::transaction(function () use ($usuario, $id) {
            $reserva = Reserva::lockForUpdate()->findOrFail($id);

            if ($reserva->usuario_id !== $usuario->codigo && !$usuario->esAdmin()) {
                abort(403, 'No tiene autorización para registrar el check-out de esta reserva.');
            }

            $tieneCheckout = CheckinReserva::where('reserva_id', $reserva->id)
                ->where('tipo', 'checkout')
                ->exists();

            if ($tieneCheckout) {
                return [
                    'success' => false,
                    'message' => 'El check-out ya fue registrado.',
                ];
            }

            if ($reserva->estado !== 'en_uso') {
                return [
                    'success' => false,
                    'message' => 'La reserva no se encuentra en uso.',
                ];
            }

            $tieneCheckin = CheckinReserva::where('reserva_id', $reserva->id)
                ->where('tipo', 'checkin')
                ->exists();

            if (!$tieneCheckin) {
                return [
                    'success' => false,
                    'message' => 'No se puede registrar el check-out porque la reserva no tiene un check-in previo.',
                ];
            }

            CheckinReserva::create([
                'reserva_id' => $reserva->id,
                'tipo' => 'checkout',
                'registrado_en' => now(),
                'origen' => 'manual',
            ]);

            $reserva->update([
                'estado' => 'completada',
            ]);

            return [
                'success' => true,
                'message' => 'Check-out registrado correctamente.',
            ];
        });

        if (!$resultado['success']) {
            return back()->withErrors([
                'checkout' => $resultado['message'],
            ]);
        }

        return back()->with('success', $resultado['message']);
    }

    public function destroy(Request $request, int $id)
    {
        $usuario = $request->user();

        $resultado = DB::transaction(function () use ($usuario, $id) {
            $reserva = Reserva::query()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($reserva->usuario_id !== $usuario->codigo && !$usuario->esAdmin()) {
                abort(403);
            }

            if ($reserva->estado === 'cancelada') {
                return [
                    'success' => false,
                    'message' => 'La reserva ya fue cancelada.',
                ];
            }

            if ($reserva->estado === 'liberada_auto') {
                return [
                    'success' => false,
                    'message' => 'La reserva ya fue liberada automáticamente.',
                ];
            }

            $tieneCheckin = CheckinReserva::where('reserva_id', $reserva->id)
                ->where('tipo', 'checkin')
                ->exists();

            $tieneCheckout = CheckinReserva::where('reserva_id', $reserva->id)
                ->where('tipo', 'checkout')
                ->exists();

            if ($reserva->estado === 'completada' || $tieneCheckout) {
                return [
                    'success' => false,
                    'message' => 'La reserva ya fue completada y no puede cancelarse.',
                ];
            }

            if ($reserva->estado === 'en_uso' || $tieneCheckin) {
                return [
                    'success' => false,
                    'message' => 'La reserva ya se encuentra en uso y no puede cancelarse.',
                ];
            }

            if (!in_array($reserva->estado, ['pendiente', 'confirmada', 'aprobada'], true)) {
                return [
                    'success' => false,
                    'message' => 'El estado actual de la reserva no permite cancelación.',
                ];
            }

            $reserva->update([
                'estado' => 'cancelada',
                'cancelado_por' => $usuario->codigo,
            ]);

            return [
                'success' => true,
                'message' => 'Reserva cancelada correctamente.',
            ];
        });

        if (!$resultado['success']) {
            return back()->withErrors([
                'reserva' => $resultado['message'],
            ]);
        }

        return back()->with('success', $resultado['message']);
    }
    
    // ... (checkout, destroy)

    // private function generarCodigoQR(int $aulaId): string ... REMOVED

    public function verificarDisponibilidad(Request $request)
    {
        // CORRECCIÓN: Usar findOrFail con el código (PK)
        $aula = Aula::where('codigo', $request->aula)->firstOrFail();
        
        return response()->json([
            'disponible' => $aula->estado === 'disponible'
        ], 200);
    }
}
