<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\ReservaActivos;
use App\Models\TipoActivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ActivoController extends Controller
{
    private const ESTADOS_PRESTAMO_ACTIVO = ['pendiente', 'aprobado', 'entregado', 'vencido'];

    public function disponibles(Request $request): Response
    {
        $query = Activo::with(['tipoActivo'])
            ->where('estado', 'disponible')
            ->whereDoesntHave('reservasActivos', function ($prestamos) {
                $prestamos->whereIn('estado', self::ESTADOS_PRESTAMO_ACTIVO);
            });

        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function ($q) use ($busqueda) {
                $q->where('descripcion', 'like', "%{$busqueda}%")
                  ->orWhere('marca', 'like', "%{$busqueda}%")
                  ->orWhere('modelo', 'like', "%{$busqueda}%")
                  ->orWhereHas('tipoActivo', function ($q) use ($busqueda) {
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
        $prestamoActivo = ReservaActivos::query()
            ->where('activo_codigo', $activo->codigo)
            ->whereIn('estado', self::ESTADOS_PRESTAMO_ACTIVO)
            ->orderByRaw("CASE estado WHEN 'pendiente' THEN 1 WHEN 'aprobado' THEN 2 WHEN 'entregado' THEN 3 WHEN 'vencido' THEN 4 ELSE 5 END")
            ->first(['id', 'activo_codigo', 'estado']);

        $disponibleParaPrestamo = $activo->estado === 'disponible' && !$prestamoActivo;

        return Inertia::render('Activos/Show', [
            'activo' => $activo,
            'disponible_para_prestamo' => $disponibleParaPrestamo,
            'motivo_no_disponible' => $this->motivoNoDisponible($activo, $prestamoActivo),
        ]);
    }

    private function motivoNoDisponible(Activo $activo, ?ReservaActivos $prestamoActivo): ?string
    {
        if ($activo->estado === 'mantenimiento') {
            return 'El activo está en mantenimiento.';
        }

        if ($activo->estado === 'baja') {
            return 'El activo fue dado de baja.';
        }

        if ($activo->estado === 'prestado') {
            return 'El activo se encuentra prestado.';
        }

        if (!$prestamoActivo) {
            return null;
        }

        return match ($prestamoActivo->estado) {
            'pendiente' => 'El activo tiene una solicitud pendiente.',
            'aprobado' => 'El activo tiene un préstamo aprobado pendiente de entrega.',
            'entregado' => 'El activo se encuentra prestado.',
            'vencido' => 'El préstamo asociado está vencido.',
            default => 'El activo no se encuentra disponible para préstamo.',
        };
    }

    /**
     * Metodo legacy para procesar solicitudes directas de activos.
     */
    public function solicitar(Request $request)
    {
        $request->validate([
            'activo_codigo' => 'required|exists:activos,codigo'
        ]);

        $usuario = $request->user();
        $tieneSanciones = ReservaActivos::where('usuario_id', $usuario->codigo)
                                  ->where('estado', 'vencido')
                                  ->exists();

        if ($tieneSanciones) {
            return Redirect::back()->with('error', 'Usuario bloqueado: Tiene devoluciones pendientes.');
        }

        try {
            DB::transaction(function () use ($request, $usuario) {
                $activo = Activo::query()
                    ->lockForUpdate()
                    ->findOrFail($request->activo_codigo);

                if ($activo->estado !== 'disponible') {
                    throw new \DomainException('El activo no se encuentra disponible para préstamo.');
                }

                $tienePrestamoActivo = ReservaActivos::query()
                    ->where('activo_codigo', $activo->codigo)
                    ->whereIn('estado', self::ESTADOS_PRESTAMO_ACTIVO)
                    ->exists();

                if ($tienePrestamoActivo) {
                    throw new \RuntimeException('El activo ya tiene una solicitud o préstamo activo.');
                }

                ReservaActivos::create([
                    'usuario_id' => $usuario->codigo,
                    'activo_codigo' => $activo->codigo,
                    'inicio_previsto' => now(),
                    'fin_previsto' => now()->addHours(2),
                    'estado' => 'pendiente'
                ]);
            });
        } catch (\DomainException $e) {
            return Redirect::back()->with('error', $e->getMessage());
        } catch (\RuntimeException $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::route('activos.disponibles')
               ->with('success', 'Solicitud de préstamo registrada correctamente.');
    }

    public function create(Request $request): Response
    {
        if (!$request->user()->esAdmin()) { abort(403, 'Acceso denegado'); }

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

        if ($validated['estado'] === 'en_deposito') {
            $validated['estado'] = 'disponible';
        }

        Activo::create($validated);

        return Redirect::route('activos.disponibles')
            ->with('success', 'Activo creado exitosamente.');
    }

    public function report(Request $request)
    {
        $activos = Activo::with(['tipoActivo'])->orderBy('descripcion')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.activos', compact('activos'));
        return $pdf->download('reporte_activos.pdf');
    }
}
