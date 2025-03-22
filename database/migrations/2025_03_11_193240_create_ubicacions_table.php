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
        Schema::create('ubicacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('codigo_postal', 20)->nullable();
            $table->integer('capacidad')->nullable();
            $table->timestamps();
            
            $table->index(['ciudad'], 'idx_ubicacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubicacion');
    }
};
