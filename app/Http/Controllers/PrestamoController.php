<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\ReservaActivos;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PrestamoController extends Controller
{
    private const ESTADOS_PRESTAMO_ACTIVO = ['pendiente', 'aprobado', 'entregado', 'vencido'];
    private const ESTADOS_PRESTAMO_FISICO_ACTIVO = ['entregado', 'vencido'];

    /**
     * Listado de prestamos con gestion administrativa.
     */
    public function index(Request $request): Response
    {
        $esAdmin = $request->user()->esAdmin();
        $estados = ['pendiente', 'aprobado', 'entregado', 'devuelto', 'vencido', 'rechazado'];

        $filtros = [
            'buscar' => trim((string) $request->query('buscar', '')),
            'estado' => (string) $request->query('estado', ''),
            'fecha_desde' => (string) $request->query('fecha_desde', ''),
            'fecha_hasta' => (string) $request->query('fecha_hasta', ''),
        ];

        $query = ReservaActivos::query()
            ->with([
                'usuario:codigo,nombre,email',
                'activo:codigo,descripcion,tipo_activo_id,estado,marca,modelo',
                'activo.tipoActivo:id,nombre',
            ]);

        if (!$esAdmin) {
            $query->where('usuario_id', $request->user()->codigo);
        }

        if ($filtros['buscar'] !== '') {
            $buscar = $filtros['buscar'];

            $query->where(function ($subquery) use ($buscar) {
                if (ctype_digit($buscar)) {
                    $subquery->orWhere('id', (int) $buscar)
                        ->orWhere('activo_codigo', (int) $buscar);
                }

                $subquery->orWhereHas('usuario', function ($usuario) use ($buscar) {
                    $usuario->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%");
                })
                    ->orWhereHas('activo', function ($activo) use ($buscar) {
                        $activo->where('descripcion', 'like', "%{$buscar}%")
                            ->orWhere('marca', 'like', "%{$buscar}%")
                            ->orWhere('modelo', 'like', "%{$buscar}%");
                    })
                    ->orWhereHas('activo.tipoActivo', function ($tipoActivo) use ($buscar) {
                        $tipoActivo->where('nombre', 'like', "%{$buscar}%");
                    });
            });
        }

        if (in_array($filtros['estado'], $estados, true)) {
            $query->where('estado', $filtros['estado']);
        }

        $fechaDesde = $this->parseDate($filtros['fecha_desde']);
        $fechaHasta = $this->parseDate($filtros['fecha_hasta']);

        if ($fechaDesde) {
            $query->where('inicio_previsto', '>=', $fechaDesde->startOfDay());
        }

        if ($fechaHasta) {
            $query->where('inicio_previsto', '<=', $fechaHasta->endOfDay());
        }

        $prestamos = $query
            ->orderByRaw("CASE estado WHEN 'entregado' THEN 1 WHEN 'pendiente' THEN 2 WHEN 'aprobado' THEN 3 WHEN 'vencido' THEN 4 WHEN 'devuelto' THEN 5 WHEN 'rechazado' THEN 6 ELSE 7 END")
            ->orderByDesc('inicio_previsto')
            ->paginate(15)
            ->withQueryString();

        $conteosQuery = ReservaActivos::query();

        if (!$esAdmin) {
            $conteosQuery->where('usuario_id', $request->user()->codigo);
        }

        $conteosPorEstado = $conteosQuery
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return Inertia::render('Prestamos/Index', [
            'prestamos' => $prestamos,
            'isAdmin' => $esAdmin,
            'modo_administrativo' => $esAdmin,
            'filtros' => $filtros,
            'estados' => $estados,
            'conteos' => [
                'total' => $esAdmin
                    ? ReservaActivos::query()->count()
                    : ReservaActivos::query()->where('usuario_id', $request->user()->codigo)->count(),
                'pendientes' => (int) ($conteosPorEstado['pendiente'] ?? 0),
                'aprobados' => (int) ($conteosPorEstado['aprobado'] ?? 0),
                'entregados' => (int) ($conteosPorEstado['entregado'] ?? 0),
                'devueltos' => (int) ($conteosPorEstado['devuelto'] ?? 0),
                'vencidos' => (int) ($conteosPorEstado['vencido'] ?? 0),
                'rechazados' => (int) ($conteosPorEstado['rechazado'] ?? 0),
            ],
        ]);
    }

    private function parseDate(string $date): ?Carbon
    {
        if ($date === '') {
            return null;
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $date);

            return $parsed && $parsed->format('Y-m-d') === $date
                ? $parsed
                : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function crear(Request $request): Response
    {
        $activoCodigo = $request->activo;
        $activo = Activo::with(['tipoActivo'])->findOrFail($activoCodigo);

        return Inertia::render('Prestamos/Crear', [
            'activo' => $activo,
        ]);
    }

    /**
     * Almacena una solicitud de prestamo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activo_codigo' => 'required|exists:activos,codigo',
            'inicio_previsto' => 'nullable|date|after_or_equal:now',
            'fin_previsto' => 'nullable|date|after:inicio_previsto',
        ]);

        $usuario = $request->user();

        try {
            DB::transaction(function () use ($request, $validated, $usuario) {
                $activo = Activo::query()
                    ->lockForUpdate()
                    ->findOrFail($validated['activo_codigo']);

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

                $inicioInput = $request->input('inicio_previsto');
                $finInput = $request->input('fin_previsto');

                $inicio = $inicioInput
                    ? Carbon::parse($inicioInput, 'America/La_Paz')->setTimezone('UTC')
                    : now();

                $fin = $finInput
                    ? Carbon::parse($finInput, 'America/La_Paz')->setTimezone('UTC')
                    : now()->addHours(2);

                ReservaActivos::create([
                    'usuario_id' => $usuario->codigo,
                    'activo_codigo' => $activo->codigo,
                    'inicio_previsto' => $inicio,
                    'fin_previsto' => $fin,
                    'estado' => 'pendiente',
                    'condicion_salida' => 'Buen estado (Solicitud)',
                ]);

                $firebase = new FirebaseService();
                $admins = \App\Models\User::whereHas('roles', function ($q) {
                    $q->whereIn('rol_id', [1, 4]);
                })->whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

                $firebase->sendToMultiple(
                    $admins,
                'Nueva Solicitud de Préstamo',
                    "El usuario {$usuario->nombre} ha solicitado el activo: {$activo->nombre}",
                    ['activo_codigo' => $activo->codigo, 'tipo' => 'nuevo_prestamo']
                );
            });

            return redirect()->route('activos.disponibles')
                ->with('success', 'Solicitud de préstamo registrada correctamente.');
        } catch (\DomainException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Error prestamo: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Ocurrio un error al procesar la solicitud. Intente nuevamente.']);
        }
    }

    /**
     * Muestra el detalle de una reserva de activo.
     */
    public function show(int $id): Response
    {
        $prestamo = ReservaActivos::with(['activo.tipoActivo', 'usuario'])
            ->findOrFail($id);

        if ($prestamo->usuario_id !== request()->user()->codigo && !request()->user()->esAdmin()) {
            abort(403);
        }

        $now = now();
        $inicioPermitido = $prestamo->inicio_previsto?->copy()->subMinutes(15);
        $finPrevisto = $prestamo->fin_previsto;
        $estado = $prestamo->estado;

        $puedeEntregar = $estado === 'aprobado'
            && $inicioPermitido
            && $finPrevisto
            && $now->greaterThanOrEqualTo($inicioPermitido)
            && $now->lessThanOrEqualTo($finPrevisto);

        return Inertia::render('Prestamos/Show', [
            'prestamo' => $prestamo,
            'isAdmin' => request()->user()->esAdmin(),
            'puede_aprobar' => $estado === 'pendiente',
            'puede_rechazar' => $estado === 'pendiente',
            'puede_entregar' => $puedeEntregar,
            'esperando_entrega' => $estado === 'aprobado'
                && $inicioPermitido
                && $now->lessThan($inicioPermitido),
            'entrega_fuera_de_plazo' => $estado === 'aprobado'
                && $finPrevisto
                && $now->greaterThan($finPrevisto),
            'puede_devolver' => in_array($estado, self::ESTADOS_PRESTAMO_FISICO_ACTIVO, true),
            'entrega_disponible_desde' => $inicioPermitido?->toIso8601String(),
        ]);
    }

    /**
     * CU-09: aprobar solicitud o registrar entrega fisica del activo.
     */
    public function entregar(Request $request, int $id)
    {
        if (!$request->user()->esAdmin()) {
            abort(403);
        }

        try {
            return DB::transaction(function () use ($id) {
                $reservaActivo = ReservaActivos::lockForUpdate()->findOrFail($id);
                $now = now();

                if ($reservaActivo->estado === 'pendiente') {
                    $reservaActivo->update([
                        'estado' => 'aprobado',
                    ]);

                    return back()->with('success', 'Solicitud de préstamo aprobada correctamente.');
                }

                if ($reservaActivo->estado === 'aprobado') {
                    $inicioPermitido = $reservaActivo->inicio_previsto->copy()->subMinutes(15);
                    $finPrevisto = $reservaActivo->fin_previsto;

                    if ($now->lessThan($inicioPermitido)) {
                        return back()->withErrors([
                            'prestamo' => 'El préstamo está aprobado, pero la entrega todavía no está disponible. Podrá registrarse desde ' . $inicioPermitido->format('d/m/Y H:i') . '.',
                        ]);
                    }

                    if ($now->greaterThan($finPrevisto)) {
                        return back()->withErrors([
                            'prestamo' => 'No se puede registrar la entrega porque el periodo autorizado ya finalizó.',
                        ]);
                    }

                    $activo = Activo::lockForUpdate()->findOrFail($reservaActivo->activo_codigo);

                    if (in_array($activo->estado, ['mantenimiento', 'baja'], true)) {
                        return back()->withErrors([
                            'prestamo' => 'No se puede registrar la entrega porque el activo ya no está operativo.',
                        ]);
                    }

                    $tieneOtroPrestamoFisicoActivo = ReservaActivos::query()
                        ->where('activo_codigo', $reservaActivo->activo_codigo)
                        ->where('id', '!=', $reservaActivo->id)
                        ->whereIn('estado', self::ESTADOS_PRESTAMO_FISICO_ACTIVO)
                        ->exists();

                    if ($tieneOtroPrestamoFisicoActivo) {
                        return back()->withErrors([
                            'prestamo' => 'No se puede registrar la entrega porque el activo ya tiene otro préstamo físico activo.',
                        ]);
                    }

                    $reservaActivo->update([
                        'estado' => 'entregado',
                        'entregado_en' => $now,
                    ]);

                    $activo->update([
                        'estado' => 'prestado',
                    ]);

                    return back()->with('success', 'Entrega del activo registrada correctamente.');
                }

                if ($reservaActivo->estado === 'entregado') {
                    return back()->withErrors(['prestamo' => 'La entrega del activo ya fue registrada.']);
                }

                if ($reservaActivo->estado === 'devuelto') {
                    return back()->withErrors(['prestamo' => 'El préstamo ya fue finalizado y devuelto.']);
                }

                if ($reservaActivo->estado === 'rechazado') {
                    return back()->withErrors(['prestamo' => 'La solicitud fue rechazada y no puede aprobarse ni entregarse.']);
                }

                if ($reservaActivo->estado === 'vencido') {
                    return back()->withErrors(['prestamo' => 'El préstamo está vencido y ya no permite registrar entrega.']);
                }

                return back()->withErrors(['prestamo' => 'El estado actual del préstamo no permite registrar esta acción.']);
            });
        } catch (\Exception $e) {
            Log::error('Error entrega activo: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Error interno procesando entrega']);
        }
    }

    /**
     * CU-09: registrar devolucion de activo.
     */
    public function rechazar(Request $request, int $id)
    {
        if (!$request->user()->esAdmin()) {
            abort(403);
        }

        try {
            return DB::transaction(function () use ($id) {
                $reservaActivo = ReservaActivos::query()
                    ->lockForUpdate()
                    ->findOrFail($id);

                $activo = Activo::query()
                    ->lockForUpdate()
                    ->findOrFail($reservaActivo->activo_codigo);

                if ($reservaActivo->estado === 'rechazado') {
                    return back()->withErrors(['prestamo' => 'La solicitud ya fue rechazada.']);
                }

                if ($reservaActivo->estado === 'aprobado') {
                    return back()->withErrors(['prestamo' => 'La solicitud ya fue aprobada y no puede rechazarse desde esta acción.']);
                }

                if ($reservaActivo->estado === 'entregado') {
                    return back()->withErrors(['prestamo' => 'El activo ya fue entregado y la solicitud no puede rechazarse.']);
                }

                if ($reservaActivo->estado === 'devuelto') {
                    return back()->withErrors(['prestamo' => 'El préstamo ya fue devuelto y no puede rechazarse.']);
                }

                if ($reservaActivo->estado === 'vencido') {
                    return back()->withErrors(['prestamo' => 'El préstamo está vencido y no puede rechazarse.']);
                }

                if ($reservaActivo->estado !== 'pendiente') {
                    return back()->withErrors(['prestamo' => 'El estado actual del préstamo no permite registrar rechazo.']);
                }

                $reservaActivo->update([
                    'estado' => 'rechazado',
                ]);

                $tieneOtroPrestamoFisicoActivo = ReservaActivos::query()
                    ->where('activo_codigo', $reservaActivo->activo_codigo)
                    ->where('id', '!=', $reservaActivo->id)
                    ->whereIn('estado', self::ESTADOS_PRESTAMO_FISICO_ACTIVO)
                    ->exists();

                if (!$tieneOtroPrestamoFisicoActivo && $activo->estado === 'prestado') {
                    $activo->update([
                        'estado' => 'disponible',
                    ]);
                }

                return back()->with('success', 'Solicitud de préstamo rechazada correctamente.');
            });
        } catch (\Exception $e) {
            Log::error('Error rechazo prestamo: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Error interno procesando rechazo']);
        }
    }

    /**
     * CU-09: registrar devolucion de activo.
     */
    public function devolver(Request $request, int $id)
    {
        if (!$request->user()->esAdmin()) {
            abort(403);
        }

        try {
            return DB::transaction(function () use ($request, $id) {
                $reservaActivo = ReservaActivos::lockForUpdate()->findOrFail($id);

                if ($reservaActivo->estado === 'devuelto') {
                    return back()->withErrors(['prestamo' => 'La devolución ya fue registrada.']);
                }

                if (!in_array($reservaActivo->estado, self::ESTADOS_PRESTAMO_FISICO_ACTIVO, true)) {
                    return back()->withErrors(['prestamo' => 'El préstamo debe estar entregado o vencido para registrar la devolución.']);
                }

                $activo = Activo::lockForUpdate()->findOrFail($reservaActivo->activo_codigo);

                $reservaActivo->update([
                    'estado' => 'devuelto',
                    'devuelto_en' => now(),
                    'condicion_retorno' => $request->input(
                        'condicion_retorno',
                        $request->input('condicion', 'Buen estado')
                    ),
                ]);

                $tieneOtroPrestamoFisicoActivo = ReservaActivos::query()
                    ->where('activo_codigo', $reservaActivo->activo_codigo)
                    ->where('id', '!=', $reservaActivo->id)
                    ->whereIn('estado', self::ESTADOS_PRESTAMO_FISICO_ACTIVO)
                    ->exists();

                if ($tieneOtroPrestamoFisicoActivo) {
                    return back()->with('success', 'Devolución registrada correctamente. El activo conserva estado prestado porque existe otro préstamo físico activo.');
                }

                $activo->update([
                    'estado' => 'disponible',
                ]);

                return back()->with('success', 'Devolución registrada correctamente. El activo volvió a estar disponible.');
            });
        } catch (\Exception $e) {
            Log::error('Error devolución activo: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Error interno procesando devolución']);
        }
    }
}
