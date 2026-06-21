<?php

namespace App\Services;

use App\Models\Reserva;
use App\Models\Activo;
use App\Models\AsientoBlockchain;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Servicio de integración con el microservicio Blockchain
 * Gestiona el registro y verificación de eventos en blockchain
 */
class BlockchainService
{
    private string $baseUrl;
    private string $apiSecret;
    
    public function __construct()
    {
        $this->baseUrl = config('services.blockchain.url', 'http://localhost:8001');
        $this->apiSecret = config('services.blockchain.secret', 'change-me-in-production');
    }
    
    /**
     * Genera firma HMAC para autenticación
     */
    private function generateSignature(string $timestamp): string
    {
        return hash_hmac('sha256', $timestamp, $this->apiSecret);
    }
    
    /**
     * Realiza petición autenticada al microservicio
     */
    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $timestamp = Carbon::now()->toIso8601String();
        $signature = $this->generateSignature($timestamp);
        
        try {
            $response = Http::withHeaders([
                'X-Signature' => $signature,
                'X-Timestamp' => $timestamp,
                'Accept' => 'application/json',
            ])->{$method}("{$this->baseUrl}{$endpoint}", $data);
            
            if (!$response->successful()) {
                throw new \Exception("Blockchain service error: " . $response->body());
            }
            
            return $response->json();
            
        } catch (\Exception $e) {
            Log::error('Blockchain service request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Registra creación de reserva en blockchain
     */
    public function registrarReservaCreada(Reserva $reserva): ?AsientoBlockchain
    {
        try {
            // Solo registrar reservas "premium" o de auditorios
            if (!$this->debeRegistrarse($reserva)) {
                return null;
            }
            
            $metadata = [
                'aula_codigo' => $reserva->aula_codigo,
                'usuario_id' => $reserva->usuario_id,
                'inicio' => $reserva->inicio->toIso8601String(),
                'fin' => $reserva->fin->toIso8601String(),
                'proposito' => $reserva->proposito,
                'asistentes_estimados' => $reserva->asistentes_estimados,
            ];
            
            $response = $this->makeRequest('post', '/api/v1/register', [
                'event_type' => 'reserva_creada',
                'reserva_id' => $reserva->id,
                'usuario_id' => $reserva->usuario_id,
                'timestamp' => now()->toIso8601String(),
                'metadata' => $metadata,
            ]);
            
            // Guardar asiento en BD local
            return AsientoBlockchain::create([
                'reserva_id' => $reserva->id,
                'tx_id' => $response['tx_id'],
                'hash' => $response['hash'],
                'bloque' => $response['bloque'] ?? null,
                'red' => $response['red'],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error registrando reserva en blockchain', [
                'reserva_id' => $reserva->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Registra modificación de reserva
     */
    public function registrarReservaModificada(Reserva $reserva, array $cambios): ?AsientoBlockchain
    {
        try {
            if (!$this->debeRegistrarse($reserva)) {
                return null;
            }
            
            $metadata = [
                'reserva_id' => $reserva->id,
                'cambios' => $cambios,
                'nuevo_estado' => $reserva->estado,
            ];
            
            $response = $this->makeRequest('post', '/api/v1/register', [
                'event_type' => 'reserva_modificada',
                'reserva_id' => $reserva->id,
                'usuario_id' => Auth::user()->codigo,
                'timestamp' => now()->toIso8601String(),
                'metadata' => $metadata,
            ]);
            
            return AsientoBlockchain::create([
                'reserva_id' => $reserva->id,
                'tx_id' => $response['tx_id'],
                'hash' => $response['hash'],
                'bloque' => $response['bloque'] ?? null,
                'red' => $response['red'],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error registrando modificación en blockchain', [
                'reserva_id' => $reserva->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Registra cancelación de reserva
     */
    public function registrarReservaCancelada(Reserva $reserva, string $motivo): ?AsientoBlockchain
    {
        try {
            if (!$this->debeRegistrarse($reserva)) {
                return null;
            }
            
            $metadata = [
                'reserva_id' => $reserva->id,
                'motivo' => $motivo,
                'cancelado_por' => $reserva->cancelado_por,
            ];
            
            $response = $this->makeRequest('post', '/api/v1/register', [
                'event_type' => 'reserva_cancelada',
                'reserva_id' => $reserva->id,
                'usuario_id' => $reserva->cancelado_por ?? Auth::user()->codigo,
                'timestamp' => now()->toIso8601String(),
                'metadata' => $metadata,
            ]);
            
            return AsientoBlockchain::create([
                'reserva_id' => $reserva->id,
                'tx_id' => $response['tx_id'],
                'hash' => $response['hash'],
                'bloque' => $response['bloque'] ?? null,
                'red' => $response['red'],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error registrando cancelación en blockchain', [
                'reserva_id' => $reserva->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Registra check-in/check-out
     */
    public function registrarCheckin(Reserva $reserva, string $tipo, string $origen): ?AsientoBlockchain
    {
        try {
            if (!$this->debeRegistrarse($reserva)) {
                return null;
            }
            
            $metadata = [
                'reserva_id' => $reserva->id,
                'tipo' => $tipo,
                'origen' => $origen,
            ];
            
            $eventType = $tipo === 'checkin' ? 'checkin' : 'checkout';
            
            $response = $this->makeRequest('post', '/api/v1/register', [
                'event_type' => $eventType,
                'reserva_id' => $reserva->id,
                'usuario_id' => $reserva->usuario_id,
                'timestamp' => now()->toIso8601String(),
                'metadata' => $metadata,
            ]);
            
            return AsientoBlockchain::create([
                'reserva_id' => $reserva->id,
                'tx_id' => $response['tx_id'],
                'hash' => $response['hash'],
                'bloque' => $response['bloque'] ?? null,
                'red' => $response['red'],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error registrando checkin en blockchain', [
                'reserva_id' => $reserva->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Registra préstamo de activo
     */
    public function registrarPrestamoCreado(int $reservaId, int $activoCodigo): void
    {
        try {
            $metadata = [
                'reserva_id' => $reservaId,
                'activo_codigo' => $activoCodigo,
            ];
            
            $this->makeRequest('post', '/api/v1/register', [
                'event_type' => 'prestamo_creado',
                'reserva_id' => $reservaId,
                'activo_codigo' => $activoCodigo,
                'usuario_id' => Auth::user()->codigo,
                'timestamp' => now()->toIso8601String(),
                'metadata' => $metadata,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error registrando préstamo en blockchain', [
                'reserva_id' => $reservaId,
                'activo_codigo' => $activoCodigo,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Registra devolución de activo
     */
    public function registrarPrestamoDevuelto(int $reservaId, int $activoCodigo): void
    {
        try {
            $metadata = [
                'reserva_id' => $reservaId,
                'activo_codigo' => $activoCodigo,
            ];
            
            $this->makeRequest('post', '/api/v1/register', [
                'event_type' => 'prestamo_devuelto',
                'reserva_id' => $reservaId,
                'activo_codigo' => $activoCodigo,
                'usuario_id' => Auth::user()->codigo,
                'timestamp' => now()->toIso8601String(),
                'metadata' => $metadata,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error registrando devolución en blockchain', [
                'reserva_id' => $reservaId,
                'activo_codigo' => $activoCodigo,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Verifica una transacción blockchain
     */
    public function verificarTransaccion(string $txId): array
    {
        return $this->makeRequest('get', "/api/v1/verify/{$txId}");
    }
    
    /**
     * Obtiene historial blockchain de una reserva
     */
    public function obtenerHistorialReserva(int $reservaId): array
    {
        return $this->makeRequest('get', "/api/v1/history/reserva/{$reservaId}");
    }
    
    /**
     * Obtiene historial blockchain de un activo
     */
    public function obtenerHistorialActivo(int $activoCodigo): array
    {
        return $this->makeRequest('get', "/api/v1/history/activo/{$activoCodigo}");
    }
    
    /**
     * Determina si una reserva debe registrarse en blockchain
     * (Solo reservas de auditorios o marcadas como "premium")
     */
    private function debeRegistrarse(Reserva $reserva): bool
    {
        // Cargar relación si no está cargada
        if (!$reserva->relationLoaded('aula')) {
            $reserva->load('aula');
        }
        
        // Registrar auditorios siempre
        if ($reserva->aula->tipo === 'auditorio') {
            return true;
        }
        
        // Agregar lógica adicional si se implementa flag "premium"
        // Por ejemplo: return $reserva->es_premium ?? false;
        
        return false;
    }

    public function registrarAuditoria(int $reservaId, array $data)
    {
        // 1. Generar Hash SHA-256 de los datos
        $hash = hash('sha256', json_encode($data));

        try {
            // 2. Aquí iría la llamada real a Ethereum/Polygon
            // $tx = Ethereum::contract(...)->send(...);
            
            // SIMULACIÓN: Generamos un TX ID falso
            $fakeTxId = '0x' . bin2hex(random_bytes(32));

            // 3. Guardar en base de datos local
            AsientoBlockchain::create([
                'reserva_id' => $reservaId,
                'tx_id' => $fakeTxId,
                'hash' => $hash,
                'bloque' => rand(1000000, 9999999), // Número de bloque simulado
                'red' => 'Polygon Testnet'
            ]);

            Log::info("Auditoría Blockchain registrada para Reserva #{$reservaId}");
            
            return true;

        } catch (\Exception $e) {
            Log::error("Fallo Blockchain: " . $e->getMessage());
            return false;
        }
    }
}