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
        Schema::create('aulas', function (Blueprint $table) {
            $table->id('codigo');
            $table->integer('capacidad');
            $table->enum('tipo', ['aula', 'laboratorio', 'auditorio', 'sala_reuniones']);
            $table->enum('estado', ['disponible', 'ocupada', 'mantenimiento', 'bloqueada'])->default('disponible');
            $table->string('qr_code', 500)->nullable();
            $table->foreignId('modulo_codigo')->constrained('modulos', 'codigo')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aulas');
    }
};
