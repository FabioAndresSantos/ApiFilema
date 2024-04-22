<?php

namespace App\Http\Controllers;

use App\Models\encuentros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\ChatController;

class EncuentrosController extends Controller
{
     //Función para enviar mensaje de invitación  a un chat con diversas conexiones 
     public function getInvitacion(Request $request){
        
        try {
            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                // Si el usuario no está autenticado, enviar una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // Obtener al usuario autenticado
            $user = Auth::userOrFail();
            
            // Obtener los valores de los parámetros
            $id_usuario_solicitado = $request->header('id_usuario_solicitado');
            $fecha_encuentro = $request->header('fecha'); 
            $hora_encuentro = $request->header('hora');
            $lugar = $request->header('lugar'); 
            $id_chat = $request->header('id_chat');
            $fecha_encuentro = $fecha_encuentro.' '.$hora_encuentro; 

            // Retornamos el id de el encuentro insertado
            $encuentro = $this->getInvitacion3($user->id, $id_usuario_solicitado, $fecha_encuentro, $lugar); 
            
                $invitacion = $this->getInvitacion1($encuentro);

                //Se crea el mensaje para enviarlo en el mismo chat
                foreach ($invitacion as $invitaciones) {
                    //Sub dividir la fecha
                    $fecha = date("Y-m-d", strtotime($invitaciones -> fecha));
                    $hora = date("H:i:s", strtotime($invitaciones -> fecha));
                    $centro_comercial = $invitaciones -> centro_comercial; 
                    $restaurante = $invitaciones -> restaurante;

                    $mensaje = 'Te quiero invitar el día: '. $fecha . ', a la hora: '. $hora . ', en el centro comercial: ' . $centro_comercial . ', en el restaurante: ' . $restaurante . ' ¿Aceptas?'; 
                    // Llamada mediante solicitud HTTP interna (HTTP Request)
                    $this->getInvitacion2($user->id, $mensaje, $id_chat, $encuentro); 
                }

            // Retornar los datos filtrados
            return response()->json(['success' =>'Mensaje enviado'], 200); 

        } catch (\Exception $e) {

            // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

    //Función para traer todos los datos del encuentro 
    public function getInvitacion1(String $encuentro){

        $invitacion = DB::table('encuentros')

            ->select('encuentros.fecha_hora_encuentro as fecha','centros_comerciales.nombre as centro_comercial', 'restaurantes.nombre as restaurante')
                ->join('lugar_encuentros', 'lugar_encuentros.id', '=', 'encuentros.id_lugar_encuentro')
                ->join('restaurantes','restaurantes.id', '=', 'lugar_encuentros.id_restaurante')
                ->join('centros_comerciales','centros_comerciales.id', '=', 'lugar_encuentros.id_centro_comercial')
                ->where('encuentros.id', $encuentro)

            ->get(); 

        return $invitacion; 
    }

    //Función para insertar el mensaje en la tabla mensajes
    public function getInvitacion2($user, $mensaje, $id_chat, $encuentro){

        DB::table('mensajes')->insert([
            'remitente_id'=>$user, 
            'mensaje'=>$mensaje,
            'chat'=>$id_chat,
            'id_encuentro'=> $encuentro

        ]); 
        
    }

    // Realiza la insersión del encuentro y guarda en la variable de id, el registro que se acaba de insertar 
    public function getInvitacion3($user, $usuario2, $fecha, $encuentro){

        $id = DB::table('encuentros')->insertGetId([
            'id_usuario_solicitante'=>$user, 
            'id_usuario_solicitado'=>$usuario2,
            'fecha_hora_encuentro'=>$fecha,
            'id_lugar_encuentro'=> $encuentro

        ]); 
        return $id; 
    }

}
