<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE reservas_activos MODIFY estado ENUM('pendiente','aprobado','entregado','devuelto','vencido','rechazado') NOT NULL DEFAULT 'pendiente'"
        );
    }

    /**
     * Reverse the migrations.
     *
     * Rollback converts rejected requests back to pending before restoring
     * the original enum, so the rejection classification is lost.
     */
    public function down(): void
    {
        DB::table('reservas_activos')
            ->where('estado', 'rechazado')
            ->update(['estado' => 'pendiente']);

        DB::statement(
            "ALTER TABLE reservas_activos MODIFY estado ENUM('pendiente','aprobado','entregado','devuelto','vencido') NOT NULL DEFAULT 'pendiente'"
        );
    }
};
