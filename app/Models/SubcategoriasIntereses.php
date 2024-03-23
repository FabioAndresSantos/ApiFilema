<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcategoriasIntereses extends Model
{
    use HasFactory;
    // Relación: Una subcategoría de interés pertenece a una categoría de interés
    public function categoria()
    {
        return $this->belongsTo(CategoriasIntereses::class);
    }
    

    
}
