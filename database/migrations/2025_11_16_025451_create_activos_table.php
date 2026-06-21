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
        Schema::create('activos', function (Blueprint $table) {
            $table->id('codigo');
            $table->string('descripcion', 500);
            $table->foreignId('tipo_activo_id')->constrained('tipo_activo')->onDelete('cascade');
            $table->enum('estado', ['disponible', 'prestado', 'mantenimiento', 'baja'])->default('disponible');
            $table->string('ubicacion_actual', 200)->nullable();
            $table->string('qr_code', 500)->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activos');
    }
};
