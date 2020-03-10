<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class GruposController extends Controller
{
    use Mk_ia_db;


    private $__modelo='\App\Grupos';

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
            }
            foreach ($request->paramsExtra as $key => $value) {
                if ($value['valor']>0) {
                    $modelo->permisos()->attach($value['id'], ['valor' => $value['valor']]);
                }
            }
        }
    }

    public function permisos(Request $request, $grupos_id)
    {
        $permisos = new \App\Permisos();
        $datos= $permisos->select('permisos.id', 'permisos.name', 'grupos_permisos.valor', 'grupos_permisos.permisos_id')->leftJoin('grupos_permisos', function ($join) use ($grupos_id) {
            $join->on('permisos.id', '=', 'permisos_id')
                 ->where('grupos_id', '=', $grupos_id);
        })->get();

        if ($request->ajax()) {
            return  $datos;
        } else {
            $d=$datos->toArray();
            return \App\Mk_helpers\Mk_db::sendData(count($d), $d);
        }
    }
}
