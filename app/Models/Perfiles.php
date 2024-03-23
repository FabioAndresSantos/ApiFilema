<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfiles extends Model
{
    use HasFactory;
    // Relación: Un perfil pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(users::class, 'id_usuario');
    }
}
