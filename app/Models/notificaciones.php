<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notificaciones extends Model
{
    use HasFactory;
    // Relación: Una notificación pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(users::class, 'id_usuario');
    }
}
