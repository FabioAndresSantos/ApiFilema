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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('genero');
            $table->string('tipoPerfil')->default('usuario');
            $table->boolean('estado_activacion_perfil')->default(true);
            $table->date('fecha_nacimiento');
            $table->string('numero_celular');
            $table->string('visibilidad');
            $table->unsignedBigInteger('ciudad_id');
            $table->foreign('ciudad_id')->references('id')->on('ciudades');
            $table->unsignedBigInteger('pais_id');
            $table->foreign('pais_id')->references('id')->on('paises');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
