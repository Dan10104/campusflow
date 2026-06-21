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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id'); 
            $table->foreign('usuario_id')->references('codigo')->on('users');
            $table->foreignId('aula_codigo')->constrained('aulas', 'codigo')->onDelete('cascade');
            $table->timestamp('inicio')->nullable();
            $table->timestamp('fin')->nullable();
            $table->text('proposito');
            $table->integer('asistentes_estimados')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'confirmada', 'en_uso', 'completada', 'cancelada', 'liberada_auto'])->default('pendiente');
            $table->string('qr_code', 500)->nullable();
            // Cambia estas dos líneas también:
            $table->foreignId('creado_por')->constrained('users', 'codigo')->onDelete('cascade');
            $table->foreignId('cancelado_por')->nullable()->constrained('users', 'codigo')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
