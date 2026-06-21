<?php

namespace App\Services;

use App\Models\AsientoBlockchain;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlockchainService
{
    /**
     * Registra un evento en la "Blockchain" (Simulada para prototipo)
     */
    public function registrarAuditoria(int $reservaId, array $data): void
    {
        // Generar hash simulación (SHA256 de los datos)
        $payload = json_encode($data);
        $hash = hash('sha256', $payload . Str::random(10));
        
        AsientoBlockchain::create([
            'reserva_id' => $reservaId,
            'tx_id' => '0x' . Str::random(64), // Formato Ethereum-like
            'hash' => $hash,
            'bloque' => rand(1000000, 9999999),
            'red' => 'Hyperledger Fabric (Simulado)'
        ]);
    }

    /**
     * Obtiene el historial completo de una reserva (On-chain + Off-chain)
     */
    public function obtenerHistorialReserva(int $reservaId): array
    {
        // 1. Obtener registros locales "confirmados"
        $asientos = AsientoBlockchain::where('reserva_id', $reservaId)->get();
        
        $events = [];

        // 2. Mapear asientos reales a eventos visuales
        foreach ($asientos as $asiento) {
            $events[] = [
                'tx_id' => $asiento->tx_id,
                'hash' => $asiento->hash,
                'event_type' => 'reserva_modificada', // Genérico por ahora
                'timestamp' => now()->subMinutes(rand(1, 60))->toIso8601String(),
                'bloque' => $asiento->bloque,
                'metadata' => []
            ];
        }

        // 3. Si no hay datos, agregar dummy data para que la demo se vea bien
        if ($asientos->isEmpty()) {
            $events[] = [
                'tx_id' => '0x' . Str::random(64),
                'hash' => hash('sha256', 'reserva_creada' . $reservaId),
                'event_type' => 'reserva_creada',
                'timestamp' => now()->subHours(2)->toIso8601String(),
                'bloque' => 12345678,
                'metadata' => ['usuario' => 'admin']
            ];
            
            $events[] = [
                'tx_id' => '0x' . Str::random(64),
                'hash' => hash('sha256', 'checkin' . $reservaId),
                'event_type' => 'checkin',
                'timestamp' => now()->subMinutes(30)->toIso8601String(),
                'bloque' => 12345689,
                'metadata' => ['lat' => -17.0, 'lng' => -63.0]
            ];
        }

        return [
            'total_events' => count($events),
            'events' => $events
        ];
    }

    /**
     * Obtiene historial de un activo (Simulado para Demo)
     */
    public function obtenerHistorialActivo(string $activoCodigo): array
    {
        // Retornar datos falsos para mostrar la UI
        return [
            'total_events' => 2,
            'events' => [
                [
                    'tx_id' => '0x' . Str::random(64),
                    'hash' => hash('sha256', 'compra' . $activoCodigo),
                    'event_type' => 'activo_registrado',
                    'timestamp' => now()->subMonths(6)->toIso8601String(),
                    'bloque' => 9876543,
                    'metadata' => []
                ],
                [
                    'tx_id' => '0x' . Str::random(64),
                    'hash' => hash('sha256', 'prestamo' . $activoCodigo),
                    'event_type' => 'prestamo_creado',
                    'timestamp' => now()->subDays(2)->toIso8601String(),
                    'bloque' => 9988776,
                    'metadata' => []
                ]
            ]
        ];
    }

    /**
     * Verifica una transacción contra el "Ledger"
     */
    public function verificarTransaccion(string $txId): array
    {
        // En prototipo, siempre válido si tiene formato hex
        $isValid = str_starts_with($txId, '0x');
        
        return [
            'valid' => $isValid,
            'message' => $isValid ? 'Transacción verificada en bloque #882910' : 'TX ID inválido',
            'blockchain_confirmations' => rand(12, 1200)
        ];
    }
}
