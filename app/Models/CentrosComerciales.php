<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentrosComerciales extends Model
{
    use HasFactory;
    // Relación: Un centro comercial pertenece a una ciudad
    public function ciudad()
    {
        return $this->belongsTo(Ciudades::class);
    }
}
