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
        Schema::create('asientos_blockchain', function (Blueprint $table) {
            $table->id('codigo');
            $table->foreignId('reserva_id')->constrained('reservas')->onDelete('cascade');
            $table->string('tx_id', 100); // Transaction hash
            $table->string('hash', 100); // Data hash
            $table->string('bloque', 50)->nullable(); // Block number
            $table->string('red', 50)->default('polygon'); // 'polygon', 'ethereum'
            $table->timestamp('creado_en')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asientos_blockchain');
    }
};
