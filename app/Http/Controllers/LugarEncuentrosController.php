<?php

namespace App\Http\Controllers;

use App\Models\lugarEncuentros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 

class LugarEncuentrosController extends Controller
{
    //Api para actualizar base de datos respecto a la invitación aceptada o rechazada
    public function updateInvitation(Request $request){
        
        try {
            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                // Si el usuario no está autenticado, enviar una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // Obtener al usuario autenticado
            $user = Auth::userOrFail();
            
            // Obtener a partir del front el id del chat
            $id_chat = $request->header('id_chat');
            $id_encuentro = $request ->header('id'); 
            $aceptado = $request ->header('aceptado');
            
            $mensaje = null; 
            $encuentro_respuesta = null;

            if ($aceptado == 1){
                $mensaje = "Invitación aceptada";
                $encuentro_respuesta = 1; 
            }else if ($aceptado == 0){
                $mensaje = "Invitación rechazada";
                $encuentro_respuesta = 0;
            }

            DB::table('encuentros') 
            ->where('id', '=', $id_encuentro)
            ->update(['aceptado' => $aceptado]); 

            DB::table('mensajes') 
            ->where('id_encuentro', '=', $id_encuentro)
            ->update(['id_encuentro' => null]); 

            DB::table('mensajes')->insert([
               'remitente_id'=>$user -> id, 
               'mensaje'=>$mensaje,
                'chat'=>$id_chat,
            ]); 

            // Retornar los datos filtrados
            return response()->json(["respuesta"=> "mensaje enviado"], 200);

        } catch (\Exception $e) {

            // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e], 500);
        }
    }
}
