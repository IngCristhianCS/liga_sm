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
        Schema::create('clasificacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneo');
            $table->foreignId('equipo_id')->constrained('equipo');
            $table->integer('partidos_jugados')->default(0);
            $table->integer('partidos_ganados')->default(0);
            $table->integer('partidos_empatados')->default(0);
            $table->integer('partidos_perdidos')->default(0);
            $table->integer('goles_a_favor')->default(0);
            $table->integer('goles_en_contra')->default(0);
            $table->integer('puntos')->default(0);
            $table->integer('posicion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clasificacions');
    }
};
