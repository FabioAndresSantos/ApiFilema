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
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('remitente_id');
            $table->foreign('remitente_id')->references('id')->on('users');
            $table->dateTime('fechaHoraMensaje')->default(now());
            $table->text('mensaje');
            $table->boolean('visto')->default(true);
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('chat');
            $table->foreign('chat')->references('id')->on('chats');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    
    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
