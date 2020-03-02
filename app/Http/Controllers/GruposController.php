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

    public function permisos(Request $request, $grupos_id)
    {
        $permisos = new \App\Permisos();
        $datos= $permisos->leftJoin('grupos_permisos', function ($join) use ($grupos_id) {
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
