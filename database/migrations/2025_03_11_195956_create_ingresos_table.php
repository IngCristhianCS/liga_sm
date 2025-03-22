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
        Schema::create('ingreso', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->decimal('monto', 10, 2);
            $table->enum('tipo', ['inscripcion','donativo','patrocinio']);
            $table->text('descripcion')->nullable();
            $table->foreignId('equipo_id')->nullable()->constrained('equipo');
            $table->foreignId('torneo_id')->nullable()->constrained('torneo');
            $table->foreignId('patrocinador_id')->nullable()->constrained('patrocinador');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingreso');
    }
};
