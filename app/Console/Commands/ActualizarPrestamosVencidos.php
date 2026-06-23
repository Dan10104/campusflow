<?php

namespace App\Console\Commands;

use App\Models\Activo;
use App\Models\ReservaActivos;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ActualizarPrestamosVencidos extends Command
{
    protected $signature = 'prestamos:actualizar-vencidos {--dry-run : Muestra los préstamos que cambiarían sin guardar cambios}';

    protected $description = 'Marca como vencidos los préstamos entregados cuyo periodo previsto ya finalizó.';

    public function handle(): int
    {
        $now = now();
        $dryRun = (bool) $this->option('dry-run');

        $query = ReservaActivos::query()
            ->with('activo:codigo,descripcion,estado')
            ->where('estado', 'entregado')
            ->where('fin_previsto', '<', $now)
            ->whereNull('devuelto_en')
            ->orderBy('id');

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->info('No hay préstamos entregados vencidos pendientes de actualizar.');

            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->warn('Simulación: no se guardará ningún cambio.');
            $this->table(
                ['ID', 'Activo', 'Estado actual', 'Nuevo estado', 'Fin previsto', 'Estado activo'],
                $query->get()->map(fn (ReservaActivos $prestamo) => [
                    $prestamo->id,
                    $prestamo->activo_codigo . ' - ' . ($prestamo->activo?->descripcion ?? 'Activo no disponible'),
                    $prestamo->estado,
                    'vencido',
                    optional($prestamo->fin_previsto)->format('Y-m-d H:i:s'),
                    $prestamo->activo?->estado ?? 'sin activo',
                ])->all()
            );
            $this->info("Préstamos que cambiarían a vencido: {$total}");

            return self::SUCCESS;
        }

        $procesados = 0;
        $activosNormalizados = 0;

        $query->select('id')->chunkById(100, function ($prestamos) use ($now, &$procesados, &$activosNormalizados) {
            foreach ($prestamos as $prestamoResumen) {
                DB::transaction(function () use ($prestamoResumen, $now, &$procesados, &$activosNormalizados) {
                    $prestamo = ReservaActivos::query()
                        ->lockForUpdate()
                        ->find($prestamoResumen->id);

                    if (
                        !$prestamo
                        || $prestamo->estado !== 'entregado'
                        || $prestamo->devuelto_en !== null
                        || !$prestamo->fin_previsto
                        || $prestamo->fin_previsto->greaterThanOrEqualTo($now)
                    ) {
                        return;
                    }

                    $activo = Activo::query()
                        ->lockForUpdate()
                        ->find($prestamo->activo_codigo);

                    $prestamo->update([
                        'estado' => 'vencido',
                    ]);

                    if (
                        $activo
                        && !in_array($activo->estado, ['mantenimiento', 'baja'], true)
                        && $activo->estado !== 'prestado'
                    ) {
                        $activo->update([
                            'estado' => 'prestado',
                        ]);
                        $activosNormalizados++;
                    }

                    $procesados++;
                });
            }
        });

        $this->info("Préstamos marcados como vencidos: {$procesados}");
        $this->info("Activos normalizados a prestado: {$activosNormalizados}");

        return self::SUCCESS;
    }
}
