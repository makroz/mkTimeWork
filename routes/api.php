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


Route::post('login', 'UsuariosController@login');
Route::post('logout', 'UsuariosController@logout');

Route::resource('Grupos', 'GruposController');
Route::group(['prefix' => 'Grupos'], function () {
    Route::post('/delete', 'GruposController@destroy');
    Route::post('/restore', 'GruposController@restore');
    Route::post('/setStatus', 'GruposController@setStatus');
    Route::post('/permisos/{grupos_id}', 'GruposController@permisos');
});
Route::resource('Permisos', 'PermisosController');
Route::group(['prefix' => 'Permisos'], function () {
    Route::post('/delete', 'PermisosController@destroy');
    Route::post('/restore', 'PermisosController@restore');
    Route::post('/setStatus', 'PermisosController@setStatus');
});

Route::resource('Roles', 'RolesController');
Route::group(['prefix' => 'Roles'], function () {
    Route::post('/delete', 'RolesController@destroy');
    Route::post('/restore', 'RolesController@restore');
    Route::post('/setStatus', 'RolesController@setStatus');
});

Route::resource('Usuarios', 'UsuariosController');
Route::group(['prefix' => 'Usuarios'], function () {
    //Route::get('/', 'UsuariosController@index');
    Route::post('/delete', 'UsuariosController@destroy');
    Route::post('/restore', 'UsuariosController@restore');
    //Route::get('/{id}', 'UsuariosController@show');
    Route::post('/setStatus', 'UsuariosController@setStatus');
    Route::post('/permisos/{grupos_id}', 'UsuariosController@permisos');
    Route::post('/permisosGrupos/{usuarios_id}', 'UsuariosController@permisosGrupos');
});
