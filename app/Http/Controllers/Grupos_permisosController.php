<?php

namespace App\Http\Controllers;

use Session;
use App\Grupos_permisos;
use App\Mk_helpers\Mk_db;
use App\Mk_helpers\Mk_forms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Grupos_permisosController extends Controller
{
    public function __construct(Request $request)
    {
        Mk_db::startDbLog(true);
        //dd( "Controler : Grupo Permisos / Accion:".Help::getAction().'::::'.App::environment().'</hr>');
        return true;
    }

    public function index(Request $request)
    {
        $token='tokens';
        $page=Mk_forms::getParam('page', 1, $token);
        $perPage=Mk_forms::getParam('per_page', 5, $token);
        $sortBy=Mk_forms::getParam('sortBy', 'id', $token);
        $order=Mk_forms::getParam('order', 'desc', $token);
        //$buscar=$request->query('buscar', '');
        //$criterio=$request->query('criterio', '');
        $buscar='';
        $buscarA=Mk_forms::getParam('buscar', '', $token);


        $where=\App\Mk_helpers\Mk_db::getWhere($buscarA);



        \App\Mk_helpers\Mk_debug::msgApi('Buscando:'.$where);
        //TODO: aqui me quede
        $consulta=Grupos_permisos::orderBy($sortBy, $order);

        //TODO: convertir esta busqueda bsica igual que la vanzada cambiando el componente en el front
        if ($buscar!='') {
            $consulta = $consulta->where($criterio, 'like', '%'.$buscar.'%');
        } else {
            if ($where!='') {
                $consulta = $consulta->whereRaw($where);
            }
        }

        if ($perPage<0) {
            $perPage=1000;
        }

        $datos = $consulta->paginate($perPage, ['*'], 'page', $page);

        if ($request->ajax()) {
            return  $datos;
        } else {
            $d=$datos->toArray();
            $data = ['ok' => $d['total'], 'data' => $d['data']];
            return Mk_db::sendData($d['total'], $d['data']);
        }
    }

    public function store(Request $request)
    {
        $datos = new Grupos_permisos();
        $datos->name = $request->name;
        $datos->status = '1';
        $datos->save();

        if (!$request->ajax()) {
            return Mk_db::sendData($datos->id);
        }
    }

    public function show($id, Request $request)
    {
        $datos = Grupos_permisos::findOrFail($id);
        return $datos;
    }

    public function edit($id)
    {
        $datos = Grupos_permisos::findOrFail($id);
        return $datos;
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            $id=$request->id;
        }

        //$datos = Grupos_permisos::findOrFail($id);
        //$datos->name = $request->name;
        //$datos->save();

        $r=Grupos_permisos::where('id', '=', $id)
        ->update([
        'name' => $request->name,
        ]);

        if (!$request->ajax()) {
            return Mk_db::sendData($r);
        }
    }

    public function destroy($id)
    {
        // TODO: Hacer el borrado de acuerdo si tiene una relacion o no
        $datos = Grupos_permisos::findOrFail($id);
        $datos->status = 'X';
        $datos->save();
    }

    public function destroyapi(Request $request)
    {
        // TODO: Hacer el borrado de acuerdo si tiene una relacion o no
        $id=explode(',', $request->id);
        $r=Grupos_permisos::wherein('id', $id)
        ->update([
        'status' => 'X',
        ]);
        if (!$request->ajax()) {
            return Mk_db::sendData($r);
        }
    }
}
