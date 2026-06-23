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
        Schema::create('aceptaciones_politicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users', 'codigo')->cascadeOnDelete();
            $table->string('version_terminos', 20);
            $table->string('version_privacidad', 20);
            $table->timestamp('aceptado_en');
            $table->string('direccion_ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('identificador_sesion', 64);
            $table->timestamps();

            $table->unique(['usuario_id', 'identificador_sesion'], 'aceptaciones_usuario_sesion_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aceptaciones_politicas');
    }
};
