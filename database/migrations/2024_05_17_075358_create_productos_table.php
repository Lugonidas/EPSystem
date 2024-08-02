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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->decimal('precio', 10, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->decimal('impuesto', 5, 2)->default(0.00);
            $table->string('unidad_medida')->nullable();
            $table->string('imagen')->nullable();
            $table->boolean('estado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
