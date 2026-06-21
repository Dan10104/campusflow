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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id('codigo'); // Esto está bien, crea la PK de esta tabla como 'codigo'
            
            // --- CORRECCIÓN AQUÍ ---
            $table->foreignId('usuario_id')->constrained('users', 'codigo')->onDelete('cascade');
            
            $table->enum('tipo', ['reserva_confirmada', 'recordatorio', 'cancelacion', 'liberacion', 'prestamo_aprobado', 'prestamo_vencido']);
            $table->enum('canal', ['email', 'push', 'sms']);
            $table->text('contenido');
            $table->enum('estado_envio', ['pendiente', 'enviado', 'fallido'])->default('pendiente');
            $table->timestamp('enviado_en')->nullable();
            $table->boolean('leida')->default(false);
            $table->timestamp('creado_en')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
