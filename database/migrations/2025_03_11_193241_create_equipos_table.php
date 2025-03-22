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
        Schema::create('equipo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('imagen', 255)->nullable();
            $table->foreignId('categoria_id')->constrained('categoria');
            $table->foreignId('entrenador_id')->nullable()->constrained('users');
            $table->timestamps();
            
            // Índices adicionales
            $table->index('categoria_id');
            $table->index('entrenador_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipo');
    }
};