<?php

namespace App\Http\Controllers;
use App\Models\chat;
use App\Http\Controllers\AuthController; 
use Dotenv\Exception\ValidationExcept;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 
use Symfony\Component\HttpKernel\Exception\HttpException;

class ChatController extends Controller{
    public function getChats(){
        try {
            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                // Si el usuario no está autenticado, enviar una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // Intentar obtener al usuario autenticado
            $user = Auth::userOrFail();

            $datos = DB::table('chats') ->get(); 
            return response()-> json($datos); 
        }catch (\Exception $e) {
            // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

    public function getMensajesChat(Request $request){
        
        try {
            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                // Si el usuario no está autenticado, enviar una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // Intentar obtener al usuario autenticado
            $user = Auth::userOrFail();
            
            // Obtener los valores de los parámetros de consulta 'remitente' y 'destinatario' enviados en la solicitud
            $usuario1 = $request->query('usuario1');
            $usuario2 = $request->query('usuario2');
            $tipo = $request->query('chat');
        
            // Ejecutar la consulta aplicando los filtros según los parámetros proporcionados
            $chat = DB::table('chats')
                        ->select('id')
                        ->where(function ($query) use ($usuario1, $usuario2, $tipo) {
                            $query->where('usuario1_id', $usuario1)
                                ->where('usuario2_id', $usuario2)
                                ->where('tipoChat', $tipo)
                                ->orWhere(function ($query) use ($usuario2, $usuario1, $tipo) {
                                    $query->where('usuario2_id', $usuario1)
                                            ->where('usuario1_id', $usuario2)
                                            ->where('tipoChat', $tipo); 
                                });
                        })
                        ->get();

            $mensajes = DB::table('mensajes')
                        ->where('chat', $chat[0]->id)
                        ->get();
            // Retornar los datos filtrados
            return response()->json($mensajes, 200);
        } catch (\Exception $e) {
            // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getChat(Request $request){

        try {
            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                // Si el usuario no está autenticado, enviar una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // Intentar obtener al usuario autenticado
            $user = Auth::userOrFail();

            // Obtener los valores de los parámetros de consulta 'remitente' y 'destinatario' enviados en la solicitud
            $usuario1 = $request->query('usuario1');
            $tipo = $request->query('chat');
            
            // Ejecutar la consulta aplicando los filtros según los parámetros proporcionados
            $chat = DB::table('chats')
            ->select('id')
                ->where(function ($query) use ($usuario1, $tipo) {
                    $query->where('chats.usuario1_id', $usuario1)
                        ->where('chats.tipoChat', $tipo)
                        ->orWhere(function ($query) use ($usuario1, $tipo) {
                            $query->where('chats.usuario2_id', $usuario1)
                                ->where('chats.tipoChat', $tipo);
                        });
                    })
                ->get();

                $mensajes = [];
                foreach ($chat as $chats) {
                    $ultimoMensaje = DB::table('mensajes')
                        ->select('mensajes.chat','mensajes.id','mensajes.mensaje', 'mensajes.visto', DB::raw('CASE WHEN chats.usuario1_id = ' . $usuario1 . ' THEN u2.nombre ELSE u1.nombre END AS nombre_usuario'))
                        ->join('chats', 'mensajes.chat', '=', 'chats.id')
                        ->join('users as u1', 'chats.usuario1_id', '=', 'u1.id')
                        ->join('users as u2', 'chats.usuario2_id', '=', 'u2.id')
                        ->where('mensajes.chat', $chats->id)
                        ->orderBy('mensajes.id', 'desc')
                        ->first(); // Obtener el primer resultado, que será el último mensaje debido al orden descendente
                
                    // Agregar el último mensaje de este chat al array de mensajes, si existe
                    if ($ultimoMensaje) {
                        $mensajes[] = $ultimoMensaje;
                    }
                }
                
                // Retornar los datos filtrados
                return response()->json($mensajes);
        } catch (\Exception $e) {
            // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

    public function getAllChat(){

        try {
            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                // Si el usuario no está autenticado, enviar una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // Intentar obtener al usuario autenticado
            $user = Auth::userOrFail();

            // Ejecutar la consulta aplicando los filtros según los parámetros proporcionados
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
                        ->select('mensajes.remitente_id','mensajes.chat','mensajes.id','mensajes.mensaje', 'mensajes.visto', DB::raw('CASE WHEN chats.usuario1_id = ' . $user->id . ' THEN u2.nombre ELSE u1.nombre END AS nombre_usuario'))
                        ->join('chats', 'mensajes.chat', '=', 'chats.id')
                        ->join('users as u1', 'chats.usuario1_id', '=', 'u1.id')
                        ->join('users as u2', 'chats.usuario2_id', '=', 'u2.id')
                        ->where('mensajes.chat', $chats->id)
                        ->orderBy('mensajes.id', 'desc')
                        ->first(); // Obtener el primer resultado, que será el último mensaje debido al orden descendente
                
                    // Verificar si el remitente del mensaje es el usuario autenticado
                    // Verificar si se encontró un mensaje
                    if ($ultimoMensaje) {
                        // Agregar el mensaje al array de mensajes con el remitente indicado
                        $mensajes[] = [
                            "chat"=>$ultimoMensaje->chat,
                            "id"=>$ultimoMensaje->id,
                            "mensaje"=>$ultimoMensaje->mensaje,
                            "visto"=>$ultimoMensaje->visto,
                            "remitente"=>$ultimoMensaje->remitente_id == $user->id ? True : False,
                            "nombre_usuario"=>$ultimoMensaje->nombre_usuario
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
    
    public function sendMensajes(Request $request){

        try {
            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                // Si el usuario no está autenticado, enviar una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // Intentar obtener al usuario autenticado
            $user = Auth::userOrFail();

            // Obtener los valores de los parámetros de consulta 'remitente' y 'destinatario' enviados en la solicitud
            $usuario1 = $request->header('usuario1');
            $mensaje = $request->header('mensaje');
            $chat = $request->header('chat');

            DB::table('mensajes')->insert([
                'remitente_id'=>$usuario1, 
                'mensaje'=>$mensaje,
                'chat'=>$chat
            ]); 
        
        } catch (\Exception $e) {
                // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
                return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
            }
    }
    
}

