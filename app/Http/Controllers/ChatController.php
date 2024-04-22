<?php

namespace App\Http\Controllers;

use App\Models\users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 

//Funciones de la pantalla Chat y Mnesajes 
class ChatController extends Controller{

    //Función para traer todos los mensajes de un chat especifico 
    //(recibe un parámetro en el header llamado id_chat)
    public function getMensajesChat(Request $request){
        
        try {
            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                // Si el usuario no está autenticado, enviar una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // Obtener al usuario autenticado
            $user = Auth::userOrFail();
            
            // Obtener a partir del front el id del chat, para traer todos los mensajes
            $id_chat = $request->header('id_chat');
            
            // Ejecutar la consulta para conectar con la tabla chats y de esa forma traer los mensajes que corresponden al id del chat
            $mensajes = DB::table('mensajes')
                        ->select("id","remitente_id","fechaHoraMensaje AS hora","mensaje","visto","chat","id_encuentro")
                        ->where('chat', $id_chat)
                        ->where ('activo', 1)
                        ->get();
            
                        foreach ($mensajes as $mensaje) {
                            $mensaje->hora = date("H:i:s", strtotime($mensaje->hora));
                        }
                        

            // Utilizar la función map para identificar si el usuario autentificado envio el mensaje
            $mensajes = $mensajes->map(function ($mensaje) use ($user) {

                // Marcar el mensaje como verdadero si el usuario autentificado fue quien envio el mensaje 
                $mensaje->remitente_id = ($mensaje->remitente_id == $user->id);
                return $mensaje;
            });

            // Retornar los datos filtrados
            return response()->json($mensajes, 200);

        } catch (\Exception $e) {

            // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

    //Función que nos trae todos los chats que tenemos, tanto amistad como relación
    public function getAllChat(){

        try {
            // Verificar si el usuario está autenticado
            if (!Auth::check()) {

                // Si el usuario no está autenticado, enviar una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // Obtener al usuario autenticado
            $user = Auth::userOrFail();

            // Consulta que nos devuelve todos los chats del usuario autenticado
            $chat = DB::table('chats')

            ->select('id')
                ->where(function ($query) use ($user) {
                    $query->where('chats.usuario1_id', $user->id)
                        ->orWhere(function ($query) use ($user) {
                            $query->where('chats.usuario2_id', $user->id); 
                        });
                    })
                ->get();

                $mensajes = [];
                foreach ($chat as $chats) {
                    $ultimoMensaje = DB::table('mensajes')
                        ->select('mensajes.fechaHoraMensaje','chats.tipoChat','mensajes.remitente_id','mensajes.chat','mensajes.id','mensajes.mensaje', 'mensajes.visto', DB::raw('CASE WHEN chats.usuario1_id = ' . $user->id . ' THEN u2.nombre ELSE u1.nombre END AS nombre_usuario'), DB::raw('CASE WHEN chats.usuario1_id = ' . $user->id. ' THEN perfil2.foto_perfil ELSE perfil1.foto_perfil END AS foto'), DB::raw('CASE WHEN chats.usuario1_id = ' . $user->id. ' THEN u2.id ELSE u1.id END AS id_persona'))
                        ->join('chats', 'mensajes.chat', '=', 'chats.id')
                        ->join('users as u1', 'chats.usuario1_id', '=', 'u1.id')
                        ->join('users as u2', 'chats.usuario2_id', '=', 'u2.id')
                        ->join('perfiles AS perfil1', 'u1.id', '=', 'perfil1.id_usuario')
                        ->join('perfiles AS perfil2', 'u2.id', '=', 'perfil2.id_usuario')
                        ->where('mensajes.chat', $chats->id)
                        ->where('mensajes.activo', 1)
                        ->orderBy('mensajes.id', 'desc')
                        // Obtener el primer resultado, que será el último mensaje debido al orden descendente
                        ->first(); 
                
                    // Verificar si el remitente del mensaje es el usuario autenticado
                    if ($ultimoMensaje) {

                        // Agregar el mensaje al array de mensajes con el remitente indicado
                        $mensajes[] = [
                            "chat"=>$ultimoMensaje->chat,
                            "id"=>$ultimoMensaje->id,
                            "tipo_chat" => $ultimoMensaje->tipoChat,
                            "mensaje"=>$ultimoMensaje->mensaje,
                            "visto"=>$ultimoMensaje->visto,
                            "remitente"=>$ultimoMensaje->remitente_id == $user->id ? True : False,
                            "nombre_destinatario"=>$ultimoMensaje->nombre_usuario,
                            "foto" => $ultimoMensaje->foto, 
                            "fecha_hora_mensaje" => $ultimoMensaje->fechaHoraMensaje,
                            "id_persona"=>$ultimoMensaje->id_persona
                        ];
                    }
                }
            
            // Retornar los datos filtrados
            return response()->json($mensajes);

        }catch (\Exception $e) {

            // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }
    
    //Función para enviar mensajes (guardarlos en la base de datos)
    //(Recibe el parametro mensaje y id_chat para identificar con quien es la conversación)
    public function sendMensajes(Request $request){

        try {
            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                // Si el usuario no está autenticado, enviar una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // Intentar obtener al usuario autenticado
            $user = Auth::userOrFail();

            // Guardamos los parametros enviados en el header
            $mensaje = $request->header('mensaje');
            $id_chat = $request->header('id_chat');
            
            // Sentencia de insersión de mensajes
            DB::table('mensajes')->insert([
                'remitente_id'=>$user -> id, 
                'mensaje'=>$mensaje,
                'chat'=>$id_chat
            ]); 
            
            return response()->json(['success' =>'Mensaje enviado'], 200); 
            
        } catch (\Exception $e) {

            // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }
}

