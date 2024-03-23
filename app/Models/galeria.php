<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class galeria extends Model
{
    use HasFactory;
    
// Relación: Una foto de la galería pertenece a un perfil de usuario
    public function perfil()
    {
        return $this->belongsTo(Perfiles::class, 'id_perfil');
    }
}
