<?php

namespace App\Http\Controllers;

use App\Models\users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(){
    // Obtiene las credenciales de la solicitud (correo electrónico y contraseña).
    $credentials = request(['email', 'password']);

    // Intenta autenticar al usuario con las credenciales proporcionadas.
    // Si las credenciales son válidas, se genera un token de acceso.
    // Si las credenciales no son válidas, devuelve un mensaje de error con un código de estado 401.
    if (! $token = auth()->attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    // Si las credenciales son válidas, devuelve una respuesta JSON que contiene el token de acceso.
    // Utiliza el método `respondWithToken` para generar la respuesta JSON.
    return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function datosUser()
    {
    // Utiliza el método `auth()->user()` para obtener los datos del usuario autenticado.
    // Devuelve una respuesta JSON que contiene los datos del usuario.
    return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(){
    // Llama al método `auth()->logout()` para cerrar sesión y revocar el token de acceso actual.
    auth()->logout();
    // Devuelve una respuesta JSON indicando que el usuario ha cerrado sesión con éxito.
    return response()->json(['message' => 'Successfully logged out']);
}

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
{
    // Utiliza el método `auth()->refresh()` para renovar el token de acceso actualmente válido.
    // Este método invalida el token actual y genera uno nuevo.
    // Luego, utiliza el método `respondWithToken` para generar una respuesta JSON que contenga el nuevo token de acceso.
    return $this->respondWithToken(auth()->refresh());
}

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
{
    // Genera una respuesta JSON que contiene el token de acceso y la información relacionada.
    return response()->json([
        'access_token' => $token, // El token de acceso generado para el usuario.
        'expires_in' => auth()->factory()->getTTL() * 60 // El tiempo de vida del token en segundos.
    ]);
}

    public function register(Request $request){
        // Validación de los datos de la solicitud
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100', // Nombre es requerido, debe ser una cadena con máximo 100 caracteres
            'email' => 'required|string|email|max:100|unique:users,email', // Email es requerido, debe ser único en la tabla 'users'
            'password' => 'required|string|min:8', // Contraseña es requerida, mínimo 8 caracteres
            'genero' => 'required|string', // Género es requerido
            'fecha_nacimiento' => 'required|date', // Fecha de nacimiento es requerida, debe ser una fecha válida
            'numero_celular' => 'required|string', // Número de celular es requerido
            'visibilidad' => 'required|string', // Visibilidad es requerida
            'ciudad_id' => 'required|exists:ciudades,id', // ciudad_id es requerido y debe existir en la tabla 'ciudades'
            'pais_id' => 'required|exists:paises,id', // pais_id es requerido y debe existir en la tabla 'paises'
        ]);
    
        // Si la validación falla, se retorna una respuesta con los errores de validación
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
    
        // Creación del usuario con los datos validados
        $user = users::create(array_merge(
            $validator->validate(), // Se obtienen los datos validados
            ['contrasena' => bcrypt($request->contrasena)] // Se hashea la contraseña antes de almacenarla
        ));
    
        // Respuesta JSON con mensaje de éxito y detalles del usuario creado
        return response()->json([
            'messaje' => 'Usuario creado exitosamente',
            'user' => $user
        ], 201);
    }
}
