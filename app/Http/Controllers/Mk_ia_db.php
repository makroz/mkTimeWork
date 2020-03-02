<?php
namespace App\Http\Controllers;

use \App\Mk_helpers\Mk_db;
use Illuminate\Http\Request;
use \App\Mk_helpers\Mk_debug;
use \App\Mk_helpers\Mk_forms;
use Illuminate\Support\Facades\DB;

const _maxRowTable=1000;
const _errorNoExiste=-1;
const _errorAlGrabar=-10;


//TODO: hacer persitente los datos de getparam al actualizar opcional
trait Mk_ia_db
{
    public function __init(Request $request)
    {
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

        $modelo=new $this->__modelo();
        if (isset($modelo->_pivotes)) {
            $consulta = $consulta->with($modelo->_pivotes);
        }

        $datos = $consulta->paginate($perPage, array_merge([$modelo->getKeyName()], $modelo->getFill()), 'page', $page);
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
        DB::beginTransaction();
        $datos = new $this->__modelo();

        $datos->fill($request->all());
        //$datos->status = '1';

        if ($datos->save()) {
            DB::commit();
            $r=$datos->id;
            $msg='';
        } else {
            DB::rollback();
            $r=-10;
            $msg=_errorAlGrabar;
        }


        if (!$request->ajax()) {
            return Mk_db::sendData($r, ($this->index($request, false))->original, $msg);
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
        DB::beginTransaction();

        $r=$this->__modelo::where('id', '=', $id)
        ->update(
            $request->all()
        );
        $msg='';

        if (!$request->ajax()) {
            if ($r==0) {
                $r=_errorNoExiste;
                $msg='Registro ya NO EXISTE';
                DB::rollback();
            }
            DB::commit();
            return Mk_db::sendData($r, ($this->index($request, false))->original, $msg);
        }
    }

    public function destroy(Request $request)
    {
        $id=explode(',', $request->id);
        DB::beginTransaction();

        $r=$this->__modelo::wherein('id', $id)
        ->delete();

        //TODO: hacer el sofdelete a las relaciones tambien
        $msg='';
        if (!$request->ajax()) {
            if ($r==0) {
                $r=_errorNoExiste;
                $msg='Registro ya NO EXISTE';
                DB::rollback();
            }
            DB::commit();
            return Mk_db::sendData($r, ($this->index($request, false))->original, $msg);
        }
    }

    public function setStatus(Request $request)
    {
        $newStatus=$request->status;
        $id=explode(',', $request->id);
        DB::beginTransaction();

        $r=$this->__modelo::wherein('id', $id)
        ->update([
        'status' => $newStatus,
        ]);
        $msg='';
        if (!$request->ajax()) {
            if ($r==0) {
                $r=_errorNoExiste;
                $msg='Registro ya NO EXISTE';
                DB::rollback();
            }
            DB::commit();
            return Mk_db::sendData($r, ($this->index($request, false))->original, $msg);
        }
    }
}
