<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use App\Models\Activo;
use App\Models\ReservaActivos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Services\FirebaseService;

class PrestamoController extends Controller
{
    /**
     * Listado de préstamos (con opción de gestión para admins)
     */
    public function index(Request $request): Response
    {
        $query = ReservaActivos::with(['activo', 'usuario'])
            ->latest();

        // Si no es admin/encargado, solo ver sus propias reservas
        if (!$request->user()->esAdmin()) {
            $query->where('usuario_id', $request->user()->codigo);
        }

        $prestamos = $query->get();

        return Inertia::render('Prestamos/Index', [
            'prestamos' => $prestamos,
            'isAdmin' => $request->user()->esAdmin(),
        ]);
    }

    public function crear(Request $request): Response
    {
        $activoCodigo = $request->activo;
        $activo = Activo::with(['tipoActivo'])->findOrFail($activoCodigo);
        
        return Inertia::render('Prestamos/Crear', [
            'activo' => $activo
        ]);
    }
    
    /**
     * Almacena una solicitud de préstamo
     */
    public function store(Request $request)
    {
        // 1. Validación de Entrada
        $validated = $request->validate([
            'activo_codigo' => 'required|exists:activos,codigo',
            // Hacemos las fechas 'nullable' por si es una solicitud inmediata (click y listo)
            'inicio_previsto' => 'nullable|date|after_or_equal:now', 
            'fin_previsto' => 'nullable|date|after:inicio_previsto',
        ]);
        
        // 2. Obtener Usuario y Activo
        $usuario = $request->user();
        
        // CORRECCIÓN 1: Buscar el activo para validar estado
        $activo = Activo::findOrFail($validated['activo_codigo']);

        // 3. Validación de Negocio (RF4 - Caja Blanca Nodo 2)
        if ($activo->estado !== 'disponible') {
            return back()->withErrors(['error' => 'El activo seleccionado ya no se encuentra disponible.']);
        }

        // 4. Transacción Atómica
        DB::beginTransaction();
        
        try {
            // Definir fechas parseando la zona horaria correcta si vienen del form
            $inicioInput = $request->input('inicio_previsto');
            $finInput = $request->input('fin_previsto');
            
            $inicio = $inicioInput 
                ? Carbon::parse($inicioInput, 'America/La_Paz')->setTimezone('UTC') 
                : now();
            
            $fin = $finInput 
                ? Carbon::parse($finInput, 'America/La_Paz')->setTimezone('UTC') 
                : now()->addHours(2);

            // CORRECCIÓN 2: Crear la reserva usando 'codigo' del usuario
            ReservaActivos::create([
                'usuario_id' => $usuario->codigo, // <--- OJO: Usar 'codigo', no 'id'
                'activo_codigo' => $activo->codigo,
                'inicio_previsto' => $inicio,
                'fin_previsto' => $fin,
                'estado' => 'pendiente',
                'condicion_salida' => 'Buen estado (Solicitud)'
            ]);
            
            // CORRECCIÓN 3: Actualizar estado del Activo para bloquearlo
            $activo->update(['estado' => 'prestado']); 
            
            DB::commit();

            // Notificar a los administradores sobre el nuevo préstamo
            $firebase = new FirebaseService();
            $admins = \App\Models\User::whereHas('roles', function($q) {
                $q->whereIn('rol_id', [1, 4]);
            })->whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

            $firebase->sendToMultiple(
                $admins,
                "Nueva Solicitud de Préstamo",
                "El usuario {$usuario->nombre} ha solicitado el activo: {$activo->nombre}",
                ['activo_codigo' => $activo->codigo, 'tipo' => 'nuevo_prestamo']
            );
            
            return redirect()->route('activos.disponibles') // O prestamos.index
                ->with('success', 'Solicitud de préstamo registrada correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            // Log para depuración real (no mostrar al usuario en producción)
            Log::error("Error préstamo: " . $e->getMessage());
            
            return back()->withErrors(['error' => 'Ocurrió un error al procesar la solicitud. Intente nuevamente.']);
        }
    }

    // ... (store method)

    /**
     * Muestra el detalle de una reserva de activo
     */
    public function show(int $id): Response
    {
        $prestamo = ReservaActivos::with(['activo.tipoActivo', 'usuario'])
            ->findOrFail($id);
            
        // Validar permisos (Basicamente si es el propio usuario o admin)
        if ($prestamo->usuario_id !== request()->user()->codigo && !request()->user()->esAdmin()) { abort(403); }

        return Inertia::render('Prestamos/Show', [
            'prestamo' => $prestamo,
            'isAdmin' => request()->user()->esAdmin()
        ]);
    }

    /**
     * CU-09: Registrar Entrega de Activo (Admin -> Usuario)
     */
    public function entregar(Request $request, int $id)
    {
        // 1. Validar que la reserva de activo exista y esté pendiente
        $reservaActivo = ReservaActivos::findOrFail($id);
        
        if ($reservaActivo->estado !== 'pendiente' && $reservaActivo->estado !== 'aprobado') {
            return back()->withErrors(['error' => 'La solicitud no está en estado pendiente o aprobada.']);
        }

        // 2. Validación de usuario (solo admin general 1 o de aulas/activos 4 puede hacer esto manual)
        if (!$request->user()->esAdmin()) { abort(403, 'No tienes permisos para entregar activos.'); }

        $now = now();
        $inicioPermitido = $reservaActivo->inicio_previsto->copy()->subMinutes(15);
        $finPrevisto = $reservaActivo->fin_previsto;

        if ($now->isAfter($finPrevisto)) {
            return back()->withErrors(['error' => 'El horario de esta reserva ya ha expirado.']);
        }

        DB::beginTransaction();
        try {
            if ($now->isBefore($inicioPermitido)) {
                // Muy temprano, solo pasa a estado aprobado
                $reservaActivo->update([
                    'estado' => 'aprobado'
                ]);
                DB::commit();
                
                return back()->with('success', 'Solicitud aprobada. Aún no es horario de recolección (hasta 15 min antes).');
            }

            // En rango (hasta el final previsto): Actualizar a entregado y mover activo
            $reservaActivo->update([
                'estado' => 'entregado',
                'entregado_en' => $now
            ]);

            // Actualizar también el estado físico del activo
            $activo = Activo::findOrFail($reservaActivo->activo_codigo);
            $activo->update(['estado' => 'prestado']);

            DB::commit();

            // Notificar al usuario que su activo ha sido entregado
            if ($reservaActivo->usuario && $reservaActivo->usuario->fcm_token) {
                $firebase = new FirebaseService();
                $firebase->sendNotification(
                    $reservaActivo->usuario->fcm_token,
                    "Activo Entregado",
                    "Se te ha entregado el activo: " . ($reservaActivo->activo->nombre ?? 'Activo'),
                    ['reserva_id' => $reservaActivo->id, 'tipo' => 'entrega_realizada']
                );
            }

            return back()->with('success', 'Activo entregado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error entrega activo: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error interno procesando entrega']);
        }
    }

    /**
     * CU-09: Registrar Devolución de Activo (Usuario -> Admin)
     */
    public function devolver(Request $request, int $id)
    {
        $reservaActivo = ReservaActivos::findOrFail($id);
        
        if ($reservaActivo->estado !== 'entregado') {
            return back()->withErrors(['error' => 'El activo no ha sido marcado como entregado']);
        }

        if (!$request->user()->esAdmin()) { abort(403, 'No tienes permisos para recibir devoluciones.'); }

        DB::beginTransaction();
        try {
            // 1. Cerrar la reserva
            $reservaActivo->update([
                'estado' => 'devuelto',
                'devuelto_en' => now(),
                'condicion_retorno' => $request->input('condicion', 'Buen estado')
            ]);

            // 2. Liberar el activo (volver a disponible)
            $activo = Activo::findOrFail($reservaActivo->activo_codigo);
            $activo->update(['estado' => 'disponible']);

            DB::commit();

            // Notificar al usuario que la devolución fue procesada
            if ($reservaActivo->usuario && $reservaActivo->usuario->fcm_token) {
                $firebase = new FirebaseService();
                $firebase->sendNotification(
                    $reservaActivo->usuario->fcm_token,
                    "Devolución Procesada",
                    "Se ha registrado la devolución de: " . ($reservaActivo->activo->nombre ?? 'Activo'),
                    ['reserva_id' => $reservaActivo->id, 'tipo' => 'devolucion_procesada']
                );
            }

            return back()->with('success', 'Activo devuelto y liberado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error devolución activo: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error interno procesando devolución']);
        }
    }
}
