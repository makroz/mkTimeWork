<?php

use \App\Mk_helpers\Mk_app;
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

Mk_app::setRuta('Roles');
Mk_app::setRuta('Permisos');
Mk_app::setRuta('Grupos',['extras'=>[
    ['post','/permisos/{grupos_id}','permisos']
]]);

Route::post('login', 'UsuariosController@login');
Route::post('logout', 'UsuariosController@logout');
Mk_app::setRuta('Usuarios',['extras'=>[
                ['post','/permisos/{grupos_id}','permisos'],
                ['post','/permisosGrupos/{usuarios_id}','permisosGrupos']
]]);

