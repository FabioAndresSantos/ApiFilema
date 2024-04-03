<?php

namespace App\Http\Controllers;

use App\Models\Perfiles;
use Auth;
use Dotenv\Exception\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PerfilesController extends Controller
{
  

     
     public function ObtenerPerfil()
     {
         try {
            if (!Auth::check()) {
                // Si el usuario no está autenticado, envia una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }
             // Intentar obtener al usuario autenticado
             $user = Auth::userOrFail();
     
             // Continuar con la lógica para obtener el perfil
             $perfiles = Perfiles::join('users', 'perfiles.id_usuario', '=', 'users.id')
                         ->select('perfiles.descripcion', 'users.nombre',
                                 'perfiles.foto_perfil')
                             ->where('perfiles.id_usuario','=', $user->id)
                         ->get();
             
             return response()->json($perfiles, 200);
         }  catch (\Exception $e) {
             // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
             return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
         }
     }
     
public function upsertPerfil(Request $request)
{
    try {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            // Si el usuario no está autenticado, enviar una respuesta 401
            return response()->json(['error' => 'No autorizado'], 401);
        }

        // Validar los datos de la solicitud
        $request->validate([
            'descripcion' => 'required|string',
            'foto_perfil' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Agrega reglas de validación para la imagen
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Buscar el perfil del usuario actual
        $perfil = Perfiles::where('id_usuario', $user->id)->first();

        // Si el perfil no existe, crear uno nuevo
        if (!$perfil) {
            $perfil = new Perfiles;
            $perfil->id_usuario = $user->id;
        }

        // Si se proporciona una nueva imagen de perfil, guardar la ubicación de la imagen en el servidor
        if ($request->hasFile("foto_perfil")) {
            $file = $request->file("foto_perfil");
            $filename = $file->getClientOriginalName();
            $filename = pathinfo($filename, PATHINFO_FILENAME);
            $name_file = str_replace(" ","_", $filename);
            $extension = $file->getClientOriginalExtension();
            $picture = date("His") ."-". $name_file ."-". $extension;
            $file->move(public_path("pictures/"), $picture);
            
            // Eliminar la imagen de perfil anterior si existe
            if ($perfil->foto_perfil) {
                unlink(public_path($perfil->foto_perfil));
            }

            // Guardar la nueva ubicación de la imagen en la base de datos
            $perfil->foto_perfil = "pictures/" . $picture;
        }


        // Actualizar la descripción del perfil
        $perfil->descripcion = $request->descripcion;
        $perfil->save();

        $data = [
            "foto_perfil"=> $perfil->foto_perfil,
            "descripcion"=> $perfil->descripcion
        ];

        return response()->json($data, $perfil->wasRecentlyCreated ? 201 : 200);
    } catch (\Exception $e) {
        Log::error('Error al crear o actualizar el perfil: ' . $e->getMessage());

        // Devolver una respuesta de error 500
        return response()->json(['error' => 'Error interno del servidor'], 500);


        // Actualizar la descripción del perfil
        $perfil->descripcion = $request->descripcion;
        $perfil->save();

        $data = [
            "foto_perfil"=> $perfil->foto_perfil,
            "descripcion"=> $perfil->descripcion
        ];

        return response()->json($data, $perfil->wasRecentlyCreated ? 201 : 200);
    } catch (\Exception $e) {
        Log::error('Error al crear o actualizar el perfil: ' . $e->getMessage());

        // Devolver una respuesta de error 500
        return response()->json(['error' => 'Error interno del servidor'], 500);
    }
}



    public function searchFriendship(Request $request){

        try {
            if (!Auth::check()) {
                // Si el usuario no está autenticado, envia una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }
             // obtiene al usuario autenticado
             $user = Auth::userOrFail();
     
             $perfiles = Perfiles::join('users', 'perfiles.id_usuario', '=', 'users.id')
            ->select('perfiles.descripcion', 'users.nombre', 'perfiles.foto_perfil')
            ->where('perfiles.id_usuario', '!=', $user->id)
            ->where(function ($query) {
                $query->where('users.visibilidad', 'amistad')
                    ->orWhere('users.visibilidad', 'ambos');
            })
            ->get();
             
             return response()->json($perfiles, 200);
         }  catch (\Exception $e) {
             // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
             return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
         }
     }

     public function searchRelationship(Request $request){

        try {
            if (!Auth::check()) {
                // Si el usuario no está autenticado, envia una respuesta 401
                return response()->json(['error' => 'No autorizado'], 401);
            }
             // obtiene al usuario autenticado
             $user = Auth::userOrFail();
     
             $perfiles = Perfiles::join('users', 'perfiles.id_usuario', '=', 'users.id')
            ->select('perfiles.descripcion', 'users.nombre', 'perfiles.foto_perfil')
            ->where('perfiles.id_usuario', '!=', $user->id)
            ->where(function ($query) {
                $query->where('users.visibilidad', 'relacion')
                    ->orWhere('users.visibilidad', 'ambos');
            })
            ->get();
             
             return response()->json($perfiles, 200);
         }  catch (\Exception $e) {
             // Si hay un error interno del servidor, enviar una respuesta 500 con información del error
             return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
         }
     }
}
