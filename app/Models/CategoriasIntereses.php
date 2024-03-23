<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriasIntereses extends Model
{
    use HasFactory;
    // Relación: Una categoría de interés tiene muchas subcategorías
    public function subcategorias()
    {
        return $this->hasMany(SubcategoriasIntereses::class);
    }
}
