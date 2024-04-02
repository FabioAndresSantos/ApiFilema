<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    //rutas para acceder desde y realizar acciones necesarias para el login 
    //se loguea y crea el token del user
    Route::post('login', 'App\Http\Controllers\AuthController@login');
    //se desloguea y vence el token 
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    //refresca el token
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
    //trae toda la informacion del user
    Route::post('datosUser', 'App\Http\Controllers\AuthController@datosUser');
    //permite crear un nuevo usuario
    Route::post('registro', 'App\Http\Controllers\AuthController@register');
    
});

Route::get('/mensajeChat',[ChatController::class, 'getMensajesChat']);
Route::get('/mensajesFinal',[ChatController::class, 'getChat']); 
Route::get('/mensajesAll',[ChatController::class, 'getAllChat']); 
Route::post('/envioMensajes',[ChatController::class, 'sendMensajes']); 