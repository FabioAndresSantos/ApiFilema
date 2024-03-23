<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bloqueos extends Model
{
    use HasFactory;
    // Relación: Un bloqueo es realizado por un usuario
    public function usuarioBloqueador()
    {
        return $this->belongsTo(users::class, 'id_usuario_bloqueador');
    }

    // Relación: Un bloqueo es realizado a un usuario
    public function usuarioBloqueado()
    {
        return $this->belongsTo(users::class, 'id_usuario_bloqueado');
    }
}
