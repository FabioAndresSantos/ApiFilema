<?php

namespace App\Http\Controllers;

use App\Models\restaurantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 

class RestaurantesController extends Controller
{
     //Función para traer todos los restaurantes de una ciudad 
    public function getRestaurantes(Request $request){
        
        try {
            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                // Si el usuario no está autenticado, enviar una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // Obtener al usuario autenticado
            $user = Auth::userOrFail();
            
            // Ejecutar la consulta aplicando los filtros de ciudad, centro comercial
            $restaurante = DB::table('restaurantes')
            
            ->select('restaurantes.id','restaurantes.nombre')
                ->join ('lugar_encuentros','restaurantes.id', '=', 'lugar_encuentros.id_restaurante')
                ->join ('centros_comerciales','lugar_encuentros.id_centro_comercial', '=', 'centros_comerciales.id')
                ->where('centros_comerciales.ciudad_id', $user-> ciudad_id )
                ->groupBy('restaurantes.id','restaurantes.nombre') 
            ->get();

            // Retornar los datos filtrados
            return response()->json($restaurante, 200);

        } catch (\Exception $e) {

            // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

    //Función para traer todos los restaurantes asociados a un centro comercial
    public function getRestaurantesCC(Request $request){
        
        try {
            // Verificar si el usuario está autenticado
            if (!Auth::check()) {
                // Si el usuario no está autenticado, enviar una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }

            // Obtener al usuario autenticado
            $user = Auth::userOrFail();
            
            $restaurante_cc = $request->header('id');

            // Ejecutar la consulta aplicando los filtros del restaurante  asociado al centro comercial
            $restaurante = DB::table('restaurantes')
            
            ->select('centros_comerciales.id','centros_comerciales.nombre')
                ->join ('lugar_encuentros','restaurantes.id', '=', 'lugar_encuentros.id_restaurante')
                ->join ('centros_comerciales','lugar_encuentros.id_centro_comercial', '=', 'centros_comerciales.id')
                ->where('centros_comerciales.ciudad_id', $user-> ciudad_id )
                ->where('restaurantes.id',$restaurante_cc)

            ->get();

            // Retornar los datos filtrados
            return response()->json($restaurante, 200);

        } catch (\Exception $e) {

            // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }
}

