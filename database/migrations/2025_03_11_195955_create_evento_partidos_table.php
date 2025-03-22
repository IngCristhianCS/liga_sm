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
        Schema::create('evento_partido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained('partido');
            $table->integer('minuto');
            $table->enum('tipo_evento', ['gol','tarjeta amarilla','tarjeta roja','sustitucion']);
            $table->foreignId('jugador_id')->nullable()->constrained('jugador');
            $table->timestamps();
            
            $table->index(['partido_id'], 'idx_evento_partido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_partido');
    }
};
