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
});
