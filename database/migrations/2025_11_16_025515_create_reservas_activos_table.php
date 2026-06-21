<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservas_activos', function (Blueprint $table) {
            $table->id();
            
            // --- CORRECCIÓN AQUÍ ---
            // Agregamos 'codigo' como segundo parámetro
            $table->foreignId('usuario_id')->constrained('users', 'codigo')->onDelete('cascade');
            
            // Esta línea ya estaba bien, asumiendo que la tabla 'activos' tiene PK 'codigo'
            $table->foreignId('activo_codigo')->constrained('activos', 'codigo')->onDelete('cascade');
            
            $table->timestamp('inicio_previsto')->nullable();
            $table->timestamp('fin_previsto')->nullable();
            $table->timestamp('entregado_en')->nullable();
            $table->timestamp('devuelto_en')->nullable();
            $table->text('condicion_salida')->nullable();
            $table->text('condicion_retorno')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'entregado', 'devuelto', 'vencido'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas_activos');
    }
};
