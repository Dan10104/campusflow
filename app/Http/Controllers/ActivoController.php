<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;
use Inertia\Inertia;
use App\Models\Activo;
use App\Models\TipoActivo;
use App\Models\ReservaActivos; // <--- Modelo Correcto

class ActivoController extends Controller
{
    public function disponibles(Request $request): Response
    {
        // Filtramos solo los disponibles según tu lógica de negocio
        $query = Activo::with(['tipoActivo'])
            ->where('estado', 'disponible'); 
        
        // Filtros
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('descripcion', 'like', "%{$busqueda}%")
                  ->orWhere('marca', 'like', "%{$busqueda}%")
                  ->orWhere('modelo', 'like', "%{$busqueda}%")
                  ->orWhereHas('tipoActivo', function($q) use ($busqueda) {
                      $q->where('nombre', 'like', "%{$busqueda}%");
                  });
            });
        }
        
        if ($request->filled('tipo')) {
            $query->where('tipo_activo_id', $request->tipo);
        }
        
        $activos = $query->orderBy('descripcion')->get();
        $tiposActivo = TipoActivo::orderBy('nombre')->get();
        
        return Inertia::render('Activos/Disponibles', [
            'activos' => $activos,
            'tiposActivo' => $tiposActivo,
            'filtros' => $request->only(['busqueda', 'tipo'])
        ]);
    }
    
    public function show(int $codigo): Response
    {
        $activo = Activo::with(['tipoActivo'])->findOrFail($codigo);
        
        return Inertia::render('Activos/Show', [
            'activo' => $activo
        ]);
    }

    /**
     * Método para procesar la solicitud (Corresponde a la Prueba de Caja Blanca)
     */
    public function solicitar(Request $request)
    {
        // Validación: 'codigo' es la PK en tu tabla activos
        $request->validate([
            'activo_codigo' => 'required|exists:activos,codigo' 
        ]);

        // (1) Nodo 1: Búsqueda del activo
        $activo = Activo::findOrFail($request->activo_codigo);

        // (2) Nodo 2: Validación de Stock (Lógica de negocio)
        if ($activo->estado !== 'disponible') {
            // (F1) Salida Falsa 1
            return Redirect::back()->with('error', 'El activo no se encuentra disponible.');
        }

        // (3) Nodo 3: Validación de usuario (Sanciones/Deudas)
        // Verificamos si tiene alguna reserva activa en estado 'vencido'
        $tieneSanciones = ReservaActivos::where('usuario_id', Auth::id())
                                  ->where('estado', 'vencido')
                                  ->exists();

        if ($tieneSanciones) {
            // (F2) Salida Falsa 2
            return Redirect::back()->with('error', 'Usuario bloqueado: Tiene devoluciones pendientes.');
        }

        // (4) Nodo 4: Registro del Préstamo (ReservaActivos)
        ReservaActivos::create([
            'usuario_id' => Auth::id(),
            'activo_codigo' => $activo->codigo, // Usamos 'codigo' no 'id'
            'inicio_previsto' => now(), // Asumimos inicio inmediato
            // 'fin_previsto' => now()->addHours(2), // Opcional: si tienes lógica de duración
            'estado' => 'pendiente'
        ]);
        
        // Actualizar estado del activo para evitar que otro lo tome
        $activo->update(['estado' => 'prestado']); 

        // (5) Nodo 5: Retorno de éxito
        // (V) Salida Verdadera
        return Redirect::route('activos.disponibles')
               ->with('success', 'Solicitud registrada correctamente.');
    }

    public function create(Request $request): Response
    {
        if (!$request->user()->esAdmin()) { abort(403, 'Acceso denegado'); }
        // Obtenemos los tipos para el select
        $tiposActivo = TipoActivo::orderBy('nombre')->get();

        return Inertia::render('Activos/Create', [
            'tiposActivo' => $tiposActivo
        ]);
    }

    public function store(Request $request)
    {
        if (!$request->user()->esAdmin()) { abort(403, 'Acceso denegado'); }
        $validated = $request->validate([
            'descripcion' => 'required|string|max:500',
            'tipo_activo_id' => 'required|exists:tipo_activo,id',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'ubicacion_actual' => 'nullable|string|max:200',
            'qr_code' => 'nullable|string|unique:activos,qr_code|max:500',
            'estado' => 'required|in:disponible,en_deposito,mantenimiento,baja',
        ]);

        Activo::create($validated);

        // Asumiendo que tienes una ruta index, o redirige a disponibles
        return Redirect::route('activos.disponibles')
            ->with('success', 'Activo creado exitosamente.');
    }

    public function report(Request $request)
    {
        // Reusamos la lógica de filtrado básica o simplemente traemos todos los disponibles
        // Para simplificar el reporte, traeremos todos los que no esten de baja, o solo disponibles?
        // El usuario pidió "lista de activos", generalmente implica inventario.
        // Pero ajustaremos a "Disponibles" si viene de esa vista o General.
        // Haremos un reporte general de items que no esten "baja" o todos.
        
        $activos = Activo::with(['tipoActivo'])->orderBy('descripcion')->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.activos', compact('activos'));
        return $pdf->download('reporte_activos.pdf');
    }
}