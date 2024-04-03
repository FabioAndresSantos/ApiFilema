<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

    'middleware' => 'api'

], function ($router) {
    //rutas para acceder desde y realizar acciones necesarias para el login 
    //se loguea y crea el token del user
    Route::post('/auth/login', 'App\Http\Controllers\AuthController@login');
    //se desloguea y vence el token 
    Route::get('/auth/logout', 'App\Http\Controllers\AuthController@logout');
    //refresca el token
    Route::get('/auth/refresh', 'App\Http\Controllers\AuthController@refresh');
    //trae toda la informacion del user
    Route::get('/auth/datosUser', 'App\Http\Controllers\AuthController@datosUser');
    //permite crear un nuevo usuario
    Route::post('/auth/registro', 'App\Http\Controllers\AuthController@register');

    //cambio de path para que se centre en el perfil
    Route::post('/perfil/upsertPerfil','App\Http\Controllers\PerfilesController@upsertPerfil');
    Route::put('/perfil/actualizarPerfil','App\Http\Controllers\PerfilesController@actualizarPerfil');

    //cambio de path para traer los usuarios de amistad o ambos  searchRelacion
    Route::get('/search/friendship','App\Http\Controllers\PerfilesController@searchFriendship');
    Route::get('/search/relationship','App\Http\Controllers\PerfilesController@searchRelationship');

});