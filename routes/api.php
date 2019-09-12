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

Route::post('api/user/login', 'permisosController@api');
Route::post('user/login', 'permisosController@api');
Route::post('grupos_permisos/fillTable', 'Grupos_permisosController@index');
Route::post('grupos_permisos/insert', 'Grupos_permisosController@store');
Route::post('grupos_permisos/edit/:id', 'Grupos_permisosController@update');
Route::post('grupos_permisos/delete/:id', 'Grupos_permisosController@destroy');
Route::post('grupos_permisos/edit', 'Grupos_permisosController@update');
Route::post('grupos_permisos/delete', 'Grupos_permisosController@destroyapi');

Route::post('permisos/fillTable', 'PermisosController@index');
Route::post('permisos/insert', 'PermisosController@store');
Route::post('permisos/edit/:id', 'PermisosController@update');
Route::post('permisos/delete/:id', 'PermisosController@destroy');
Route::post('permisos/edit', 'PermisosController@update');
Route::post('permisos/delete', 'PermisosController@destroyapi');
