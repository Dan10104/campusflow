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
        Schema::create('usuarios_roles', function (Blueprint $table) {
            
            $table->unsignedBigInteger('usuario_id');
        $table->unsignedBigInteger('rol_id');

        // Definir las claves foráneas usando las columnas recién definidas
        // Ahora son compatibles (BIGINT UNSIGNED vs. BIGINT UNSIGNED)
        $table->foreign('rol_id')->references('codigo')->on('roles');
        $table->foreign('usuario_id')->references('codigo')->on('users');

        // Definir la clave primaria compuesta (para evitar duplicados de rol/permiso)
        $table->primary(['usuario_id', 'rol_id']);

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_roles');
    }
};
