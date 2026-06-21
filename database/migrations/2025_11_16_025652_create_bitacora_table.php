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
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id('codigo');
            $table->string('entidad', 100); 
            $table->string('accion', 50); 
            
            // --- CORRECCIÓN AQUÍ ---
            $table->foreignId('usuario_id')->constrained('users', 'codigo')->onDelete('cascade');
            
            $table->json('detalle_json')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->timestamp('creado_en')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};
