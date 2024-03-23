<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lugarEncuentros extends Model
{
    use HasFactory;
    // Relación: Un lugar de encuentro pertenece a un centro comercial
    public function centroComercial()
    {
        return $this->belongsTo(CentrosComerciales::class, 'id_centro_comercial');
    }

    // Relación: Un lugar de encuentro pertenece a un restaurante
    public function restaurante()
    {
        return $this->belongsTo(restaurantes::class, 'id_restaurante');
    }
}
