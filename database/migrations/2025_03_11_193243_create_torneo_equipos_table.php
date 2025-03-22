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
        Schema::create('torneo_equipo', function (Blueprint $table) {
            $table->foreignId('torneo_id')->constrained("torneo");
            $table->foreignId('equipo_id')->constrained("equipo");
            $table->primary(['torneo_id', 'equipo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torneo_equipo');
    }
};
