<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserva;
use App\Services\IoTService;
use App\Models\CheckinReserva;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckAulaInactivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aulas:check-inactivity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica aulas en uso y las libera si no detecta movimiento por sensores IoT';

    /**
     * Execute the console command.
     */
    public function handle(IoTService $iotService)
    {
        $this->info('Iniciando verificación de inactividad de aulas...');
        
        // 1. Obtener reservas actualmente "en uso"
        $reservasEnCurso = Reserva::where('estado', 'en_uso')->get();

        if ($reservasEnCurso->isEmpty()) {
            $this->info('No hay aulas en uso en este momento.');
            return;
        }

        $liberadas = 0;

        foreach ($reservasEnCurso as $reserva) {
            // 2. Consultar Servicio IoT
            $this->info("Verificando aula: " . $reserva->aula_codigo);
            $hayMovimiento = $iotService->detectarMovimiento($reserva->aula_codigo);

            if (!$hayMovimiento) {
                // Verificar si acaba de hacer Check-in (darle 15 mins de gracia)
                $ultimoCheckin = $reserva->checkins()
                    ->where('tipo', 'checkin')
                    ->latest('registrado_en')
                    ->first();

                if ($ultimoCheckin && Carbon::parse($ultimoCheckin->registrado_en)->diffInMinutes(now()) < 15) {
                    $this->info("Aula {$reserva->aula_codigo} vacía, pero check-in reciente. Se mantiene.");
                    continue;
                }

                // 3. Liberar el aula
                $this->warn("Liberando aula {$reserva->aula_codigo} por inactividad detectada.");
                
                $reserva->update([
                    'estado' => 'cancelada', 
                    'cancelado_por' => 'SISTEMA_IOT'
                ]);

                // Registrar evento
                CheckinReserva::create([
                    'reserva_id' => $reserva->id,
                    'tipo' => 'checkout_forzado',
                    'registrado_en' => now(),
                    'origen' => 'sistema_iot'
                ]);

                $liberadas++;
            }
        }

        $this->info("Proceso finalizado. Aulas liberadas: $liberadas");
    }
}
