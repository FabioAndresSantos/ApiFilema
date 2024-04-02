<?php

namespace App\Http\Controllers;
use App\Models\chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class ChatController extends Controller{
    public function getChats(){
        $datos = DB::table('chats') ->get(); 
        return response()-> json($datos); 
    }

    public function getMensajesChat(Request $request){
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
        return response()->json($mensajes);
    }
    
    public function getChat(Request $request){
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
    }  
    public function getAllChat(Request $request){
        // Obtener los valores de los parámetros de consulta 'remitente' y 'destinatario' enviados en la solicitud
        $usuario1 = $request->query('usuario1');
        
        // Ejecutar la consulta aplicando los filtros según los parámetros proporcionados
        $chat = DB::table('chats')
        ->select('id')
            ->where(function ($query) use ($usuario1) {
                $query->where('chats.usuario1_id', $usuario1)
                    ->orWhere(function ($query) use ($usuario1) {
                        $query->where('chats.usuario2_id', $usuario1); 
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
    }
    
    public function sendMensajes(Request $request){
        // Obtener los valores de los parámetros de consulta 'remitente' y 'destinatario' enviados en la solicitud
        $usuario1 = $request->header('usuario1');
        $mensaje = $request->header('mensaje');
        $chat = $request->header('chat');

        DB::table('mensajes')->insert([
            'remitente_id'=>$usuario1, 
            'mensaje'=>$mensaje,
            'chat'=>$chat
        ]); 
    }

}

