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
        Schema::create('sensores', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['pir', 'ultrasonico', 'camara'])->default('pir');
            $table->string('hardware_id', 100)->unique();
            $table->enum('estado', ['activo', 'inactivo', 'error'])->default('activo');
            $table->timestamp('ultimo_heartbeat')->nullable();
            $table->foreignId('aula_codigo')->nullable()->constrained('aulas', 'codigo')->onDelete('set null');
            $table->integer('bateria')->nullable(); // 0-100
            $table->integer('senal')->nullable(); // RSSI
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensores');
    }
};
