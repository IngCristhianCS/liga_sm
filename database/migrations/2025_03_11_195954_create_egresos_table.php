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
        Schema::create('egreso', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->decimal('monto', 10, 2);
            $table->enum('tipo', ['arbitraje','mantenimiento','organizacion']);
            $table->text('descripcion')->nullable();
            $table->foreignId('partido_id')->nullable()->constrained('partido');
            $table->foreignId('torneo_id')->nullable()->constrained('torneo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egreso');
    }
};
