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
        Schema::table('users', function (Blueprint $table) {
            $table->string('numero_identificacion')->nullable();
            $table->boolean('rol')->default(false);
            $table->string('usuario')->nullable();
            $table->boolean('estado')->default(true);
            $table->string('imagen')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('numero_identificacion');
            $table->dropColumn('rol');
            $table->dropColumn('usuario');
            $table->dropColumn('estado');
            $table->dropColumn('imagen');
        });
    }
};
