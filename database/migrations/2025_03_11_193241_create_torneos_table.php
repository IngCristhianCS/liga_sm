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
        Schema::create('torneo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->foreignId('categoria_id')->constrained('categoria');
            $table->foreignId('temporada_id')->constrained('temporada');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['activo','finalizado'])->default('activo');
            $table->foreignId('campeon_id')->nullable()->constrained('equipo');
            $table->timestamps();
            
            $table->index(['fecha_inicio', 'fecha_fin'], 'idx_torneo_fechas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torneo');
    }
};
