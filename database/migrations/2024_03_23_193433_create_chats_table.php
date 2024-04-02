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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario1_id');
            $table->unsignedBigInteger('usuario2_id');
            $table->foreign('usuario1_id')->references('id')->on('users');
            $table->foreign('usuario2_id')->references('id')->on('users');
            $table->unsignedBigInteger('tipoChat');
            $table->foreign('tipoChat')->references('id')->on('tipochat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
