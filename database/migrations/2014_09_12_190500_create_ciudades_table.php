<?php

use App\Models\Ciudades;
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
        Schema::create('ciudades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('pais_id');
            $table->foreign('pais_id')->references('id')->on('paises');
            $table->timestamps();
        });
        // Insertar datos de prueba
        $argentina = ['nombre' => 'Buenos Aires', 'pais_id' => 1];
        $brasil = ['nombre' => 'Sao Paulo', 'pais_id' => 2];
        $chile = ['nombre' => 'Bogota', 'pais_id' => 3];

        Ciudades::insert([$argentina, $brasil, $chile]);
        // Agrega más ciudades según sea necesario
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciudades');
    }
};
