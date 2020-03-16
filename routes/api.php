<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::resource('Grupos_permisos', 'Grupos_permisosController');

Route::get('Grupos_permisos/', 'Grupos_permisosController@index');
Route::post('Grupos_permisos/delete', 'Grupos_permisosController@destroy');
Route::post('Grupos_permisos/setStatus', 'Grupos_permisosController@setStatus');

Route::resource('Grupos', 'GruposController');
Route::group(['prefix' => 'Grupos'], function () {
    Route::get('/', 'GruposController@index');
    Route::post('/delete', 'GruposController@destroy');
    Route::post('/setStatus', 'GruposController@setStatus');
    Route::post('/permisos/{grupos_id}', 'GruposController@permisos');
});
Route::resource('Permisos', 'PermisosController');
Route::group(['prefix' => 'Permisos'], function () {
    Route::get('/', 'PermisosController@index');
    Route::post('/delete', 'PermisosController@destroy');
    Route::post('/setStatus', 'PermisosController@setStatus');
});

Route::resource('Roles', 'RolesController');
Route::group(['prefix' => 'Roles'], function () {
    Route::get('/', 'RolesController@index');
    Route::post('/delete', 'RolesController@destroy');
    Route::post('/setStatus', 'RolesController@setStatus');
});

Route::resource('Usuarios', 'UsuariosController');
Route::group(['prefix' => 'Usuarios'], function () {
    Route::get('/', 'UsuariosController@index');
    Route::post('/delete', 'UsuariosController@destroy');
    Route::post('/setStatus', 'UsuariosController@setStatus');
    Route::post('/permisos/{grupos_id}', 'UsuariosController@permisos');
});
