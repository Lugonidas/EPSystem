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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('numero_identificacion')->nullable();
            $table->string('celular')->nullable();
            $table->string('email')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('barrio')->nullable();
            $table->string('direccion')->nullable();
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

/*     26_142947 */

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
