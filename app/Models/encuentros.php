<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class encuentros extends Model
{
    use HasFactory;
    // Relación: Un encuentro es solicitado por un usuario
    public function usuarioSolicitante()
    {
        return $this->belongsTo(users::class, 'id_usuario_solicitante');
    }

    // Relación: Un encuentro es solicitado a un usuario
    public function usuarioSolicitado()
    {
        return $this->belongsTo(users::class, 'id_usuario_solicitado');
    }
}
