<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Activo;
use App\Models\AsientoBlockchain;
use App\Services\BlockchainService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlockchainController extends Controller
{
    public function __construct(
        private BlockchainService $blockchainService
    ) {}
    
    /**
     * Muestra la vista de historial blockchain de una reserva
     */
    public function showReservaHistory(int $reservaId): Response
    {
        $reserva = Reserva::with(['aula.modulo.facultad', 'usuario', 'asientoBlockchain'])
            ->findOrFail($reservaId);
        
        // Verificar permisos (opcional)
        // $this->authorize('view', $reserva);
        
        try {
            // Obtener historial desde el microservicio
            $historialBlockchain = $this->blockchainService->obtenerHistorialReserva($reservaId);
            
            // Formatear eventos para la vista
            $eventos = collect($historialBlockchain['events'] ?? [])->map(function ($evento) {
                return [
                    'tx_id' => $evento['tx_id'],
                    'hash' => $evento['hash'],
                    'tipo' => $this->translateEventType($evento['event_type']),
                    'timestamp' => $evento['timestamp'],
                    'bloque' => $evento['bloque'],
                    'metadata' => $evento['metadata'],
                ];
            });
            
        } catch (\Exception $e) {
            // Si falla el microservicio, mostrar solo info local
            $eventos = collect();
            $error = 'No se pudo obtener el historial blockchain: ' . $e->getMessage();
        }
        
        return Inertia::render('Blockchain/ReservaHistory', [
            'reserva' => [
                'id' => $reserva->id,
                'aula' => [
                    'codigo' => $reserva->aula->codigo,
                    'nombre' => $reserva->aula->modulo->nombre,
                    'tipo' => $reserva->aula->tipo,
                ],
                'facultad' => $reserva->aula->modulo->facultad->sigla,
                'usuario' => $reserva->usuario->name,
                'inicio' => $reserva->inicio,
                'fin' => $reserva->fin,
                'estado' => $reserva->estado,
                'proposito' => $reserva->proposito,
            ],
            'eventos' => $eventos,
            'asientos_locales' => $reserva->asientoBlockchain,
            'total_eventos' => $historialBlockchain['total_events'] ?? 0,
            'error' => $error ?? null,
        ]);
    }
    
    /**
     * Muestra la vista de historial blockchain de un activo
     */
    public function showActivoHistory(int $activoCodigo): Response
    {
        $activo = Activo::with(['tipoActivo', 'reservasActivos'])
            ->findOrFail($activoCodigo);
        
        try {
            // Obtener historial desde el microservicio
            $historialBlockchain = $this->blockchainService->obtenerHistorialActivo($activoCodigo);
            
            // Formatear eventos para la vista
            $eventos = collect($historialBlockchain['events'] ?? [])->map(function ($evento) {
                return [
                    'tx_id' => $evento['tx_id'],
                    'hash' => $evento['hash'],
                    'tipo' => $this->translateEventType($evento['event_type']),
                    'timestamp' => $evento['timestamp'],
                    'bloque' => $evento['bloque'],
                    'metadata' => $evento['metadata'],
                ];
            });
            
        } catch (\Exception $e) {
            $eventos = collect();
            $error = 'No se pudo obtener el historial blockchain: ' . $e->getMessage();
        }
        
        return Inertia::render('Blockchain/ActivoHistory', [
            'activo' => [
                'codigo' => $activo->codigo,
                'descripcion' => $activo->descripcion,
                'tipo' => $activo->tipoActivo->nombre,
                'estado' => $activo->estado,
                'marca' => $activo->marca,
                'modelo' => $activo->modelo,
                'ubicacion_actual' => $activo->ubicacion_actual,
            ],
            'eventos' => $eventos,
            'total_eventos' => $historialBlockchain['total_events'] ?? 0,
            'error' => $error ?? null,
        ]);
    }
    
    /**
     * Verifica la integridad de una transacción
     */
    public function verificarTransaccion(Request $request)
    {
        $request->validate([
            'tx_id' => 'required|string',
        ]);
        
        try {
            $resultado = $this->blockchainService->verificarTransaccion($request->tx_id);
            
            return response()->json([
                'success' => true,
                'data' => $resultado,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error verificando transacción: ' . $e->getMessage(),
            ], 400);
        }
    }
    
    /**
     * Traduce tipos de eventos a español
     */
    private function translateEventType(string $eventType): string
    {
        return match($eventType) {
            'reserva_creada' => 'Reserva Creada',
            'reserva_modificada' => 'Reserva Modificada',
            'reserva_cancelada' => 'Reserva Cancelada',
            'prestamo_creado' => 'Préstamo Creado',
            'prestamo_devuelto' => 'Préstamo Devuelto',
            'checkin' => 'Check-in',
            'checkout' => 'Check-out',
            default => ucfirst(str_replace('_', ' ', $eventType)),
        };
    }
}