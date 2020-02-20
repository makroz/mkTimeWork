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
        Mk_debug::__init();
        Mk_db::startDbLog();
        return true;
    }
    public function index(Request $request, $_debug=true)
    {
        $token='tokens';//TODO: hacer queel token sea automatico se recupere de cada usuario que se conecte unico, validar el token con ip etc.
        $page=Mk_forms::getParam('page', 1, $token);
        $perPage=Mk_forms::getParam('per_page', 5, $token);
        $sortBy=Mk_forms::getParam('sortBy', 'id', $token);
        $order=Mk_forms::getParam('order', 'desc', $token);
        $buscarA=Mk_forms::getParam('buscar', '', $token);

        $where=Mk_db::getWhere($buscarA);

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
            return Mk_db::sendData($d['total'], $d['data'], '', $_debug);
        }
    }

    public function store(Request $request)
    {
        $datos = new $this->__modelo();
        $datos->name = $request->name;
        $datos->status = '1';
        $datos->save();

        if (!$request->ajax()) {
            return Mk_db::sendData($datos->id, ($this->index($request, false))->original);
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

        $r=$this->__modelo::where('id', '=', $id)
        ->update([
        'name' => $request->name,
        ]);
        $msg='';

        if (!$request->ajax()) {
            if ($r==0) {
                $r=-1;
                $msg='Registro ya NO EXISTE';
            }
            return Mk_db::sendData($r, ($this->index($request, false))->original, $msg);
        }
    }

    public function destroy(Request $request)
    {
        $id=explode(',', $request->id);
        $r=$this->__modelo::wherein('id', $id)
        ->delete();
        //->update([
        //'status' => 'X',
        //]);

        //TODO: hacer el sofdelete a las relaciones tambien
        $msg='';
        if (!$request->ajax()) {
            if ($r==0) {
                $r=-1;
                $msg='Registro ya NO EXISTE';
            }

            return Mk_db::sendData($r, ($this->index($request, false))->original, $msg);
        }
    }

    public function setStatus(Request $request)
    {
        $newStatus=$request->status;
        $id=explode(',', $request->id);
        $r=$this->__modelo::wherein('id', $id)
        ->update([
        'status' => $newStatus,
        ]);
        $msg='';
        if (!$request->ajax()) {
            if ($r==0) {
                $r=-1;
                $msg='Registro ya NO EXISTE';
            }
            return Mk_db::sendData($r, ($this->index($request, false))->original, $msg);
        }
    }
}
