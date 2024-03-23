<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class matches extends Model
{
    use HasFactory;
    // Relación: Un match es solicitado por un usuario
    public function usuarioSolicitante()
    {
        return $this->belongsTo(users::class, 'id_usuario_solicitante');
    }

    // Relación: Un match es solicitado a un usuario
    public function usuarioSolicitado()
    {
        return $this->belongsTo(users::class, 'id_usuario_solicitado');
    }
}
