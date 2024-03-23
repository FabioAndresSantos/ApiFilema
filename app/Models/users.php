<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class users extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre', 'email', 'password', 'genero', 'tipoPerfil',
        'estado_activacion_perfil', 'fecha_nacimiento', 'numero_celular',
        'visibilidad', 'ciudad_id', 'pais_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    //identificador
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */

     
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudades::class);
    }

    // Relación: Un usuario pertenece a un país
    public function pais()
    {
        return $this->belongsTo(Paises::class);
    }


    // Relación: Un usuario tiene un perfil
    public function perfil()
    {
        return $this->hasOne(Perfiles::class, 'id_usuario');
    }

    // Relación: Un usuario puede enviar muchos chats
    public function chatsEnviados()
    {
        return $this->hasMany(chat::class, 'remitente_id');
    }

    // Relación: Un usuario puede recibir muchos chats
    public function chatsRecibidos()
    {
        return $this->hasMany(chat::class, 'destinatario_id');
    }

    // Relación: Un usuario puede solicitar muchos matches
    public function matchesSolicitados()
    {
        return $this->hasMany(matches::class, 'id_usuario_solicitante');
    }

    // Relación: Un usuario puede recibir muchos matches
    public function matchesRecibidos()
    {
        return $this->hasMany(matches::class, 'id_usuario_solicitado');
    }

    // Relación: Un usuario puede realizar muchos bloqueos
    public function bloqueosRealizados()
    {
        return $this->hasMany(bloqueos::class, 'id_usuario_bloqueador');
    }

    // Relación: Un usuario puede ser bloqueado por muchos usuarios
    public function bloqueosRecibidos()
    {
        return $this->hasMany(bloqueos::class, 'id_usuario_bloqueado');
    }

    // Relación: Un usuario puede recibir muchas notificaciones
    public function notificaciones()
    {
        return $this->hasMany(notificaciones::class);
    }

    // Relación: Un usuario puede solicitar muchos encuentros
    public function lugarEncuentroSolicitado()
    {
        return $this->hasMany(encuentros::class, 'id_usuario_solicitante');
    }

    // Relación: Un usuario puede ser solicitado para muchos encuentros
    public function lugarEncuentroRecibido()
    {
        return $this->hasMany(encuentros::class, 'id_usuario_solicitado');
    }

    // Relación: Un usuario puede tener muchas fotos en la galería
    public function galeria()
    {
        return $this->hasMany(galeria::class, 'id_perfil');
    }

    // Relación: Un usuario puede tener muchos intereses
    public function intereses()
    {
        return $this->hasMany(Usuario_intereses::class);
    }
}
