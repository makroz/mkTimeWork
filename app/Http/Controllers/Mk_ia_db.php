<?php
namespace App\Http\Controllers;

use \App\Mk_helpers\Mk_db;
use \App\Mk_helpers\Mk_forms;
use \App\Mk_helpers\Mk_debug;
use Illuminate\Http\Request;

const _maxRowTable=1000;

//TODO: hacer persitente los datos de getparam al actualizar opcional
trait Mk_ia_db
{
    public function __init(Request $request)
    {
        Mk_db::startDbLog(true);
        //dd( "Controler : Grupo Permisos / Accion:".Help::getAction().'::::'.App::environment().'</hr>');
        return true;
    }
    public function index(Request $request)
    {
        $token='tokens';//TODO: hacer queel token sea automatico se recupere de cada usuario que se conecte unico, validar el token con ip etc.
        $page=Mk_forms::getParam('page', 1, $token);
        $perPage=Mk_forms::getParam('per_page', 5, $token);
        $sortBy=Mk_forms::getParam('sortBy', 'id', $token);
        $order=Mk_forms::getParam('order', 'desc', $token);
        //$buscar=$request->query('buscar', '');
        //$criterio=$request->query('criterio', '');
        $buscarA=Mk_forms::getParam('buscar', '', $token);


        $where=Mk_db::getWhere($buscarA);

        Mk_debug::msgApi('Buscando:'.$where);

        $consulta=$this->__modelo::orderBy($sortBy, $order);

        if ($where!='') {
            $consulta = $consulta->whereRaw($where);
        }

        if ($perPage<0) {
            $perPage=_maxRowTable;
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
        $datos = new $this->__modelo();
        $datos->name = $request->name;
        $datos->status = '1';
        $datos->save();

        if (!$request->ajax()) {
            return Mk_db::sendData($datos->id);
        }
    }

    public function show($id, Request $request)
    {
        $datos = $this->__modelo::findOrFail($id);
        return $datos;
    }

    public function edit($id)
    {
        $datos = $this->__modelo::findOrFail($id);
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

        //TODO: revisar errorres si es que no existe el registro;
        $r=$this->__modelo::where('id', '=', $id)
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
        $id=$request->id;
        $r=$this->__modelo::wherein('id', $id)
        ->update([
        'status' => 'X',
        ]);
        if (!$request->ajax()) {
            return Mk_db::sendData($r);
        }
    }

    public function destroyapi(Request $request)
    {
        // TODO: Hacer el borrado de acuerdo si tiene una relacion o no
        $id=explode(',', $request->id);
        $r=$this->__modelo::wherein('id', $id)
        ->update([
        'status' => 'X',
        ]);
        if (!$request->ajax()) {
            return Mk_db::sendData($r);
        }
    }
}
