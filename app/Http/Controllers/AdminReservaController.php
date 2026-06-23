<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminReservaController extends Controller
{
    private const ESTADOS_RESERVA = [
        'pendiente',
        'aprobada',
        'confirmada',
        'en_uso',
        'completada',
        'cancelada',
        'liberada_auto',
    ];

    public function index(Request $request): Response
    {
        if (!$request->user()->esAdmin()) {
            abort(403);
        }

        $filtros = [
            'buscar' => trim((string) $request->query('buscar', '')),
            'estado' => (string) $request->query('estado', ''),
            'aula' => (string) $request->query('aula', ''),
            'fecha_desde' => (string) $request->query('fecha_desde', ''),
            'fecha_hasta' => (string) $request->query('fecha_hasta', ''),
        ];

        $query = Reserva::query()
            ->select([
                'id',
                'usuario_id',
                'aula_codigo',
                'inicio',
                'fin',
                'proposito',
                'asistentes_estimados',
                'estado',
                'created_at',
                'updated_at',
            ])
            ->with([
                'usuario:codigo,nombre,email',
                'aula:codigo,capacidad,tipo,estado,modulo_codigo',
                'aula.modulo:codigo,nombre,ubicacion,facultad_codigo',
                'aula.modulo.facultad:codigo,nombre,sigla',
                'checkins:id,reserva_id,tipo,registrado_en,origen',
            ])
            ->withExists([
                'checkins as tiene_checkin' => fn ($checkins) => $checkins->where('tipo', 'checkin'),
                'checkins as tiene_checkout' => fn ($checkins) => $checkins->where('tipo', 'checkout'),
            ]);

        if ($filtros['buscar'] !== '') {
            $buscar = $filtros['buscar'];

            $query->where(function ($subquery) use ($buscar) {
                if (ctype_digit($buscar)) {
                    $subquery->orWhere('id', (int) $buscar)
                        ->orWhere('aula_codigo', (int) $buscar);
                }

                $subquery->orWhere('proposito', 'like', "%{$buscar}%")
                    ->orWhereHas('usuario', function ($usuario) use ($buscar) {
                        $usuario->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('email', 'like', "%{$buscar}%");
                    })
                    ->orWhereHas('aula.modulo', function ($modulo) use ($buscar) {
                        $modulo->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('ubicacion', 'like', "%{$buscar}%");
                    })
                    ->orWhereHas('aula.modulo.facultad', function ($facultad) use ($buscar) {
                        $facultad->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('sigla', 'like', "%{$buscar}%");
                    });
            });
        }

        if (in_array($filtros['estado'], self::ESTADOS_RESERVA, true)) {
            $query->where('estado', $filtros['estado']);
        }

        if ($filtros['aula'] !== '' && ctype_digit($filtros['aula'])) {
            $query->where('aula_codigo', (int) $filtros['aula']);
        }

        $fechaDesde = $this->parseDate($filtros['fecha_desde']);
        $fechaHasta = $this->parseDate($filtros['fecha_hasta']);

        if ($fechaDesde) {
            $query->where('inicio', '>=', $fechaDesde->startOfDay());
        }

        if ($fechaHasta) {
            $query->where('inicio', '<=', $fechaHasta->endOfDay());
        }

        $reservas = $query
            ->orderByRaw("CASE estado WHEN 'en_uso' THEN 1 WHEN 'pendiente' THEN 2 WHEN 'confirmada' THEN 3 WHEN 'aprobada' THEN 3 ELSE 4 END")
            ->orderByDesc('inicio')
            ->paginate(15)
            ->withQueryString();

        $conteosPorEstado = Reserva::query()
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $aulas = Aula::query()
            ->select(['codigo', 'tipo', 'modulo_codigo'])
            ->with([
                'modulo:codigo,nombre,facultad_codigo',
                'modulo.facultad:codigo,nombre,sigla',
            ])
            ->orderBy('codigo')
            ->get();

        return Inertia::render('Admin/Reservas/Index', [
            'reservas' => $reservas,
            'filtros' => $filtros,
            'estados' => self::ESTADOS_RESERVA,
            'aulas' => $aulas,
            'conteos' => [
                'total' => Reserva::query()->count(),
                'pendientes' => (int) ($conteosPorEstado['pendiente'] ?? 0),
                'confirmadas' => (int) ($conteosPorEstado['confirmada'] ?? 0) + (int) ($conteosPorEstado['aprobada'] ?? 0),
                'en_uso' => (int) ($conteosPorEstado['en_uso'] ?? 0),
                'completadas' => (int) ($conteosPorEstado['completada'] ?? 0),
                'canceladas' => (int) ($conteosPorEstado['cancelada'] ?? 0),
            ],
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        if (!$request->user()->esAdmin()) {
            abort(403);
        }

        $reserva = Reserva::query()
            ->select([
                'id',
                'usuario_id',
                'aula_codigo',
                'inicio',
                'fin',
                'proposito',
                'asistentes_estimados',
                'estado',
                'created_at',
                'updated_at',
            ])
            ->with([
                'usuario:codigo,nombre,email',
                'aula:codigo,capacidad,tipo,estado,modulo_codigo',
                'aula.modulo:codigo,nombre,ubicacion,facultad_codigo',
                'aula.modulo.facultad:codigo,nombre,sigla',
                'checkins' => fn ($checkins) => $checkins
                    ->select(['id', 'reserva_id', 'tipo', 'registrado_en', 'origen'])
                    ->orderBy('registrado_en'),
            ])
            ->findOrFail($id);

        $tieneCheckin = $reserva->checkins->contains('tipo', 'checkin');
        $tieneCheckout = $reserva->checkins->contains('tipo', 'checkout');

        return Inertia::render('Admin/Reservas/Show', [
            'reserva' => $reserva,
            'tiene_checkin' => $tieneCheckin,
            'tiene_checkout' => $tieneCheckout,
            'puede_aprobar' => $reserva->estado === 'pendiente',
            'puede_rechazar' => $reserva->estado === 'pendiente',
            'puede_checkout' => $reserva->estado === 'en_uso' && $tieneCheckin && !$tieneCheckout,
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
}
