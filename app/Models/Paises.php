<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paises extends Model
{
    use HasFactory;

    // Relación: Un país tiene muchas ciudades
    public function ciudades()
    {
        return $this->hasMany(Ciudades::class);
    }
}
