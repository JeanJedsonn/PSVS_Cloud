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
        Schema::dropIfExists('juegos');
        Schema::create('juegos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string("titulo");
            $table->text("descripcion")->nullable();
            $table->decimal("precio", 10, 2)->nullable();
            $table->string("plataforma");

            $table->string("imgURL");
            $table->string("imgLowURL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // no es posible revertir esta accion
    }
};
