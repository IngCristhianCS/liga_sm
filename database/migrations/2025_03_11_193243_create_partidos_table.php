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
        Schema::create('partido', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->foreignId('ubicacion_id')->nullable()->constrained('ubicacion');
            $table->foreignId('equipo_local_id')->constrained('equipo');
            $table->foreignId('equipo_visitante_id')->constrained('equipo');
            $table->foreignId('torneo_id')->constrained('torneo');
            $table->integer('goles_equipo_local')->default(0);
            $table->integer('goles_equipo_visitante')->default(0);
            $table->foreignId('arbitro_principal_id')->nullable()->constrained('users');
            $table->foreignId('arbitro_asistente1_id')->nullable()->constrained('users');
            $table->foreignId('arbitro_asistente2_id')->nullable()->constrained('users');
            $table->time('hora_inicio')->nullable();
            $table->foreignId('jornada_id')->nullable()->constrained('jornada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partido');
    }
};
