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
        Schema::create('aula_caracteristicas', function (Blueprint $table) {
            $table->foreignId('aula_codigo')->constrained('aulas', 'codigo')->onDelete('cascade');
            $table->foreignId('caracteristica_id')->constrained('caracteristicas_aula')->onDelete('cascade');
            $table->primary(['aula_codigo', 'caracteristica_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aula_caracteristicas');
    }
};
