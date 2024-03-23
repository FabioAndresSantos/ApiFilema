<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario_intereses extends Model
{
    use HasFactory;
    // Relación: Un interés pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(users::class);
    }

    // Relación: Un interés pertenece a una subcategoría de interés
    public function subcategoria()
    {
        return $this->belongsTo(SubcategoriasIntereses::class);
    }
}
