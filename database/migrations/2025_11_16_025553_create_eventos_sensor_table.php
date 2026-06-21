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
        Schema::create('eventos_sensor', function (Blueprint $table) {
            $table->id('codigo');
            $table->foreignId('sensor_id')->constrained('sensores')->onDelete('cascade');
            $table->string('tipo_evento', 50); // 'presencia', 'ausencia', 'heartbeat'
            $table->timestamp('registrado_en');
            $table->json('payload_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_sensor');
    }
};
