<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles_permisos', function (Blueprint $table) {
            // 1. Definimos las columnas.
            // Tienen que ser unsignedBigInteger porque tus padres usan bigIncrements
            $table->unsignedBigInteger('rol_id');
            $table->unsignedBigInteger('permiso_id');

            // 2. Definimos las claves foráneas
            // IMPORTANTE: Debemos especificar ->references('codigo') explícitamente
            // porque Laravel busca 'id' por defecto, y tu llave se llama 'codigo'.
            $table->foreign('rol_id')
                  ->references('codigo')
                  ->on('roles')
                  ->onDelete('cascade');

            $table->foreign('permiso_id')
                  ->references('codigo')
                  ->on('permisos')
                  ->onDelete('cascade');

            // 3. Clave primaria compuesta para evitar duplicados
            $table->primary(['rol_id', 'permiso_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles_permisos');
    }
};