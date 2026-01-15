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
        Schema::create('juego_monedas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('juego_id')->index();
            $table->foreignId('moneda_id')->index();

            $table->string('precio_oferta');
            $table->string('precio_original');
            $table->string('precio_oferta_anterior');
            $table->string('precio_original_anterior');

            $table->string('subtitulos');
            $table->string('audio');
            $table->string('link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juego_monedas');
    }
};
