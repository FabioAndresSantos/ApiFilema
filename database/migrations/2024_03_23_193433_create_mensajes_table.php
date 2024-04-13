<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; 

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
            $table->dateTime('fechaHoraMensaje')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('mensaje');
            $table->boolean('visto')->default(true);
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('chat');
            $table->foreign('chat')->references('id')->on('chats');
            $table->unsignedBigInteger('id_encuentro')->nullable();
            $table->foreign('id_encuentro')->references('id')->on('encuentros')->onDelete('set null');
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
