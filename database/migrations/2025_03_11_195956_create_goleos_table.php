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
        Schema::create('goleo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_id')->constrained('jugador');
            $table->foreignId('partido_id')->constrained('partido');
            $table->foreignId('torneo_id')->constrained('torneo');
            $table->integer('cantidad_goles');
            $table->date('fecha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goleo');
    }
};
