<?php
namespace App\Observers;

use App\Models\Reserva;
use App\Services\BlockchainService;

class ReservaObserver
{
    public function __construct(
        private BlockchainService $blockchainService
    ) {}
    
    /**
     * Se ejecuta después de crear una reserva
     */
    public function created(Reserva $reserva): void
    {
        // Registrar en blockchain de forma asíncrona (opcional)
        dispatch(function () use ($reserva) {
            $this->blockchainService->registrarReservaCreada($reserva);
        })->afterResponse();
    }
    
    /**
     * Se ejecuta después de actualizar una reserva
     */
    public function updated(Reserva $reserva): void
    {
        // Solo registrar si cambió el estado
        if ($reserva->isDirty('estado')) {
            $cambios = [
                'estado_anterior' => $reserva->getOriginal('estado'),
                'estado_nuevo' => $reserva->estado,
            ];
            
            dispatch(function () use ($reserva, $cambios) {
                $this->blockchainService->registrarReservaModificada($reserva, $cambios);
            })->afterResponse();
        }
    }
}