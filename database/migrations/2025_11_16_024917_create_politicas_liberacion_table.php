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
        Schema::create('politicas_liberacion', function (Blueprint $table) {
            $table->id('codigo');
            $table->string('nivel', 50); // 'global', 'facultad', 'aula'
            $table->integer('umbral_minutos')->default(20); // Minutos sin presencia
            $table->integer('ventana_histeresis_seg')->default(300); // 5 minutos
            $table->boolean('activo')->default(true);
            $table->timestamp('creado_en')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('politicas_liberacion');
    }
};
