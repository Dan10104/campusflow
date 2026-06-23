<?php

namespace App\Console\Commands;

use App\Models\CheckinReserva;
use App\Models\Reserva;
use App\Services\IoTService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAulaInactivity extends Command
{
    protected $signature = 'aulas:check-inactivity';

    protected $description = 'Verifica aulas en uso y las libera si no detecta movimiento por sensores IoT';

    public function handle(IoTService $iotService): int
    {
        $this->info('Iniciando verificacion de inactividad de aulas...');

        $reservasEnCurso = Reserva::where('estado', 'en_uso')->get();

        if ($reservasEnCurso->isEmpty()) {
            $this->info('No hay aulas en uso en este momento.');

            return self::SUCCESS;
        }

        $liberadas = 0;

        foreach ($reservasEnCurso as $reserva) {
            $this->info('Verificando aula: ' . $reserva->aula_codigo);

            $hayMovimiento = $iotService->detectarMovimiento($reserva->aula_codigo);

            if ($hayMovimiento) {
                continue;
            }

            $ultimoCheckin = $reserva->checkins()
                ->where('tipo', 'checkin')
                ->latest('registrado_en')
                ->first();

            if ($ultimoCheckin && Carbon::parse($ultimoCheckin->registrado_en)->diffInMinutes(now()) < 15) {
                $this->info("Aula {$reserva->aula_codigo} vacia, pero con check-in reciente. Se mantiene.");
                continue;
            }

            DB::transaction(function () use ($reserva, &$liberadas) {
                $reservaBloqueada = Reserva::query()
                    ->lockForUpdate()
                    ->find($reserva->id);

                if (!$reservaBloqueada || $reservaBloqueada->estado !== 'en_uso') {
                    return;
                }

                $yaTieneCheckout = $reservaBloqueada->checkins()
                    ->where('tipo', 'checkout')
                    ->exists();

                $reservaBloqueada->update([
                    'estado' => 'liberada_auto',
                    'cancelado_por' => null,
                ]);

                if (!$yaTieneCheckout) {
                    CheckinReserva::create([
                        'reserva_id' => $reservaBloqueada->id,
                        'tipo' => 'checkout',
                        'registrado_en' => now(),
                        'origen' => 'manual',
                    ]);
                }

                $liberadas++;
            });
        }

        $this->info("Proceso finalizado. Aulas liberadas: {$liberadas}");

        return self::SUCCESS;
    }
}
