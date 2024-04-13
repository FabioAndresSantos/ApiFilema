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
        Schema::create('encuentros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario_solicitante');
            $table->foreign('id_usuario_solicitante')->references('id')->on('users');
            $table->unsignedBigInteger('id_usuario_solicitado');
            $table->foreign('id_usuario_solicitado')->references('id')->on('users');
            $table->dateTime('fecha_hora_encuentro');
            $table->unsignedBigInteger('id_lugar_encuentro');
            $table->foreign('id_lugar_encuentro')->references('id')->on('lugar_encuentros');
            $table->boolean('aceptado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encuentros');
    }
};