<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Reserva;
use App\Models\ReservaActivos;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Dashboard de "Centro de Aprobaciones"
     */
    public function index(Request $request)
    {
        if (!$request->user()->esAdmin()) { abort(403, 'Acceso denegado'); }
        // Obtener reservas de aulapendientes
        $reservasPendientes = Reserva::with(['aula.modulo', 'usuario'])
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($reserva) {
                // Flatten aula info for easier UI
                $reserva->aula->nombre = $reserva->aula->modulo->nombre . ' - ' . $reserva->aula->tipo;
                return $reserva;
            });
            
        // Obtener préstamos de activos pendientes
        $prestamosPendientes = ReservaActivos::with(['activo', 'usuario'])
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'asc')
            ->get();
            
        return Inertia::render('Admin/Declaracion', [
            'reservasPendientes' => $reservasPendientes,
            'prestamosPendientes' => $prestamosPendientes
        ]);
    }
    
    /**
     * Aprobar Reserva de Aula
     */
    public function aprobarReserva(Request $request, int $id)
    {
        if (!$request->user()->esAdmin()) { abort(403, 'Acceso denegado'); }
        $reserva = Reserva::findOrFail($id);
        
        if($reserva->estado !== 'pendiente') {
            return back()->withErrors(['error' => 'La reserva no está pendiente']);
        }
        
        $reserva->update(['estado' => 'confirmada']); // O 'aprobada'
        
        // Aquí se podría disparar notificación email
        
        return back()->with('success', 'Reserva aprobada');
    }

    /**
     * Rechazar Reserva de Aula
     */
    public function rechazarReserva(Request $request, int $id)
    {
        if (!$request->user()->esAdmin()) { abort(403, 'Acceso denegado'); }
        $reserva = Reserva::findOrFail($id);
        
        if($reserva->estado !== 'pendiente') {
            return back()->withErrors(['error' => 'La reserva no está pendiente']);
        }
        
        $reserva->update([
            'estado' => 'cancelada',
            'cancelado_por' => $request->user()->codigo
        ]);
        
        return back()->with('success', 'Reserva rechazada');
    }
}
