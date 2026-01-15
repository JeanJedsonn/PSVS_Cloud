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
        Schema::create('monedas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string("region");
            $table->string("direccion");
            $table->string("simbolo_moneda");
            $table->decimal("tasa_usd", 10, 5);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monedas');
    }
};
