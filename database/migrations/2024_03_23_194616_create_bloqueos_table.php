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
        Schema::create('bloqueos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_Usuario_Bloqueador');
            $table->foreign('id_Usuario_Bloqueador')->references('id')->on('users');
            $table->unsignedBigInteger('id_Usuario_Bloqueado');
            $table->foreign('id_Usuario_Bloqueado')->references('id')->on('users');
            $table->dateTime('fecha_bloqueo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloqueos');
    }
};