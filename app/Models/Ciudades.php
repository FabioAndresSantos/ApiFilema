<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudades extends Model
{
    use HasFactory;

    // Relación: Una ciudad pertenece a un país
    public function pais()
    {
        return $this->belongsTo(Paises::class);
    }
    // Relación: Una ciudad tiene muchos usuarios
    public function usuarios()
    {
        return $this->hasMany(users::class);
    }
}
