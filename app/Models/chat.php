<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class chat extends Model
{
    use HasFactory;
    // Relación: Un chat pertenece a un usuario remitente
    public function remitente()
    {
        return $this->belongsTo(users::class, 'remitente_id');
    }

    // Relación: Un chat pertenece a un usuario destinatario
    public function destinatario()
    {
        return $this->belongsTo(users::class, 'destinatario_id');
    }
}
