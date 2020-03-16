<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class UsuariosController extends Controller
{
    use Mk_ia_db;


    private $__modelo='\App\Usuarios';

    public function __construct(Request $request)
    {
        $this->__init($request);
        return true;
    }

    public function afterDel($ids, $modelo, $error=0)
    {
        if ($error>=0) {
            foreach ($ids as $key => $value) {
                if (($value!='')and($value>0)) {
                    $modelo->id=$value ;
                    $modelo->permisos()->detach();
                }
            }
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
        $datos= $permisos->select('permisos.id', 'permisos.name', 'usuarios_permisos.valor', 'usuarios_permisos.permisos_id')->leftJoin('usuarios_permisos', function ($join) use ($usuarios_id) {
            $join->on('permisos.id', '=', 'permisos_id')
                 ->where('usuarios_id', '=', $usuarios_id);
        })->orderBy('permisos.name')->get();

        if ($request->ajax()) {
            return  $datos;
        } else {
            $d=$datos->toArray();
            return \App\Mk_helpers\Mk_db::sendData(count($d), $d, $this->permisosGrupos($request, $usuarios_id, false)->original);
        }
    }

    public function permisosGrupos(Request $request, $usuarios_id, $debug=true)
    {
        $grupos_id=$request->grupos;
        if (!is_array($grupos_id)) {
            $grupos_id=[];
        }

        $permisos = new \App\Permisos();
        $datos= $permisos->select(\Illuminate\Support\Facades\DB::raw('BIT_OR(grupos_permisos.valor) as valor'), 'permisos.id')->leftJoin('grupos_permisos', function ($join) use ($grupos_id) {
            $join->on('permisos.id', '=', 'permisos_id')
                 ->wherein('grupos_id', $grupos_id);
        })->groupBy('grupos_id', 'id')->orderBy('permisos.name')->get();

        if ($request->ajax()) {
            return  $datos;
        } else {
            $d=$datos->toArray();
            return \App\Mk_helpers\Mk_db::sendData(count($d), $d, '', $debug);
        }
    }
}
