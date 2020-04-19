<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class UsuariosController extends Controller
{
    use Mk_ia_db;
    public $_autorizar='';
    public $_validators=[];


    private $__modelo='\App\Usuarios';

    public function __construct(Request $request)
    {
        $this->__init($request);
        $this->_validator= [
            'name' => 'required',
            'email' => 'required|email|unique:usuarios,email,'.$request->input('id'),
            'pass' => 'sometimes|required|min:8',
            'roles_id' => 'integer',
            'status' => 'in:0,1'
        ];
        return true;
    }

    public function beforeSave(Request $request, $modelo, $action=1)
    {
        if ($action==1){
            $modelo->pass= sha1($modelo->pass);
        }
    }

    public function afterSave(Request $request, $modelo, $error=0, $action=1)
    {
        if ($error>=0) {
            if ($action==2) {//modificar
                $modelo->id=$request->id;
                $modelo->permisos()->detach();
                $modelo->grupos()->detach();
            }
            foreach ($request->paramsExtra['permisos'] as $key => $value) {
                if ($value['valor']>0) {
                    $modelo->permisos()->attach($value['id'], ['valor' => $value['valor']]);
                }
            }
            foreach ($request->paramsExtra['grupos'] as $key => $value) {
                if ($value>0) {
                    $modelo->grupos()->attach($value);
                }
            }
        }
    }

    public function permisos(Request $request, $usuarios_id)
    {
        $permisos = new \App\Permisos();
        $datos= $permisos->select('permisos.id', 'permisos.name', 'usuarios_permisos.valor', 'permisos.slug')
        ->leftJoin('usuarios_permisos', function ($join) use ($usuarios_id) {
            $join->on('permisos.id', '=', 'permisos_id')
                 ->where('usuarios_id', '=', $usuarios_id);
        })->orderBy('permisos.name')->get();

        if ($request->ajax()) {
            return  $datos;
        } else {
            $d=$datos->toArray();
            return \App\Mk_helpers\Mk_db::sendData(count($d), $d, $this->permisosGrupos($request, 0, false));
        }
    }

    public function permisosGrupos(Request $request, $usuarios_id=0, $debug=true)
    {
        $grupos_id=$request->grupos;
        if (!is_array($grupos_id)) {
            $grupos_id=[];
        }

        $permisos = new \App\Permisos();
        $datos= $permisos->select('permisos.id', \Illuminate\Support\Facades\DB::raw('BIT_OR(grupos_permisos.valor) as valor'))->leftJoin('grupos_permisos', function ($join) use ($grupos_id) {
            $join->on('permisos.id', '=', 'permisos_id')
                 ->wherein('grupos_id', $grupos_id);
        })->groupBy('permisos.id')->orderBy('permisos.name')->get();

        if ($request->ajax()) {
            return  $datos;
        } else {
            $d=$datos->toArray();
            return \App\Mk_helpers\Mk_db::sendData(count($d), $d, '', $debug);
        }
    }


    public function login(Request $request)
    {

        $Auth=\App\Mk_helpers\Mk_auth\Mk_auth::get();
        $msg='';
        $user=$Auth->login( $request->username, $request->password);
        if (empty($user)) {
            $r=_errorLogin;
            $msg='Login Erroneo';
            $user=[];
        } else {
            $r=$user['id'];
            //print_r($d);
        }
        return \App\Mk_helpers\Mk_db::sendData($r, $user, $msg);
    }
}
