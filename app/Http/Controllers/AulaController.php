<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use App\Models\Aula;
use App\Models\Modulo;
use App\Models\Facultad;
use App\Models\Reserva;
use App\Models\User;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AulaController extends Controller
{
    public function disponibles(Request $request): Response
    {
        $query = Aula::with([
            'modulo.facultad',
            'caracteristicas'
        ])->where('estado', 'disponible');
        
        // Filtros
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->whereHas('modulo', function($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                      ->orWhereHas('facultad', function($q) use ($busqueda) {
                          $q->where('nombre', 'like', "%{$busqueda}%")
                            ->orWhere('sigla', 'like', "%{$busqueda}%");
                      });
                })->orWhere('codigo', 'like', "%{$busqueda}%");
            });
        }
        
        if ($request->filled('facultad')) {
            $query->whereHas('modulo', function($q) use ($request) {
                $q->where('facultad_codigo', $request->facultad);
            });
        }
        
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        
        if ($request->filled('capacidad')) {
            $query->where('capacidad', '>=', $request->capacidad);
        }
        
        // Filtrar por disponibilidad en horario específico
        if ($request->filled(['fecha', 'hora_inicio', 'hora_fin'])) {
            $inicio = Carbon::parse($request->fecha . ' ' . $request->hora_inicio, 'America/La_Paz')->setTimezone('UTC');
            $fin = Carbon::parse($request->fecha . ' ' . $request->hora_fin, 'America/La_Paz')->setTimezone('UTC');
            
            $query->whereDoesntHave('reservas', function($q) use ($inicio, $fin) {
                $q->whereIn('estado', ['confirmada', 'aprobada', 'en_uso'])
                  ->where(function($q) use ($inicio, $fin) {
                      // Verificar solapamiento
                      $q->where('inicio', '<', $fin)
                        ->where('fin', '>', $inicio);
                  });
            });
        }
        
        $aulas = $query->orderBy('capacidad', 'desc')->get();
        
        $facultades = Facultad::orderBy('sigla')->get();
        
        return Inertia::render('Aulas/Disponibles', [
            'aulas' => $aulas,
            'facultades' => $facultades,
            'tipos' => ['aula', 'laboratorio', 'auditorio', 'sala_reuniones'],
            'filtros' => $request->only(['busqueda', 'facultad', 'tipo', 'capacidad', 'fecha', 'hora_inicio', 'hora_fin'])
        ]);
    }
    
    /**
     * Muestra calendario de disponibilidad de un aula
     */
    public function calendario(int $codigo): Response
    {
        $aula = Aula::with(['modulo.facultad'])->findOrFail($codigo);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Verificar si es admin (Roles 1 o 4)
        $isAdmin = $user->roles()->whereIn('usuarios_roles.rol_id', [1, 4])->exists(); 
        // Nota: Asumiendo que la tabla pivote es usuarios_roles y la FK es rol_id. 
        // Si falla, verificar User.php relacion roles().
        // En User.php: belongsToMany(Role::class, 'usuarios_roles', 'usuario_id', 'rol_id', ...)
        
        $reservasQuery = Reserva::where('aula_codigo', $codigo)
            ->where('inicio', '>=', now())
            ->where('inicio', '<=', now()->addDays(60))
            ->with(['usuario'])
            ->orderBy('inicio');

        if ($isAdmin) {
            // Admin ve todo (pendiente, confirmada, cancelada incluso si quiere, pero filtramos las utiles)
            $reservasQuery->whereIn('estado', ['confirmada', 'aprobada', 'en_uso', 'pendiente']);
        } else {
            // Usuario normal ve: Confirmadas de todos + Sus propias pendientes
            $reservasQuery->where(function($q) use ($user) {
                $q->whereIn('estado', ['confirmada', 'aprobada', 'en_uso'])
                  ->orWhere(function($subQ) use ($user) {
                      $subQ->where('usuario_id', $user->codigo)
                           ->where('estado', 'pendiente');
                  });
            });
        }

        $reservas = $reservasQuery->get();
        
        return Inertia::render('Aulas/Calendario', [
            'aula' => $aula,
            'reservas' => $reservas,
            'isAdmin' => $isAdmin // Pasar flag a la vista
        ]);
    }

    public function report()
    {
        $aulas = Aula::with(['modulo.facultad'])->orderBy('codigo')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.aulas', compact('aulas'));
        return $pdf->download('reporte_aulas.pdf');
    }

    public function reportCalendario(int $codigo)
    {
        $aula = Aula::with(['modulo.facultad'])->findOrFail($codigo);
        
        // Misma lógica de reservas que la vista
        $reservas = Reserva::where('aula_codigo', $codigo)
            ->whereIn('estado', ['confirmada', 'aprobada', 'en_uso'])
            ->where('inicio', '>=', now())
            ->where('inicio', '<=', now()->addDays(30))
            ->with(['usuario'])
            ->orderBy('inicio')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.calendario', compact('aula', 'reservas'));
        return $pdf->download('reporte_calendario_aula_' . $codigo . '.pdf');
    }
}
