<?php
namespace App\Http\Controllers;

use \App\Mk_helpers\Mk_db;
use \App\Mk_helpers\Mk_auth\Mk_auth;
use \Illuminate\Http\Request;
use \App\Mk_helpers\Mk_debug;
use \App\Mk_helpers\Mk_forms;
use Illuminate\Support\Facades\DB;

const _maxRowTable=1000;
const _errorNoExiste=-1;
const _errorAlGrabar=-10;
const _errorAlGrabar2=-11;
const _errorLogin=-1000;
const _errorNoAutenticado=-1001;




trait Mk_ia_db
{
    public function __init(Request $request)
    {

        if (isset($this->_autorizar)){
            $Auth=Mk_auth::getInstance();
            if (!$Auth->isLogin()){
                $Auth->blockData(true);
                //TODO: ver lamanera que tampoco ejecute ninguna consulta a la BD
            }
        }
        Mk_db::startDbLog();
        return true;
    }
    public function index(Request $request, $_debug=true)
    {
        $token=Mk_auth::get()->getToken();
        Mk_debug::msgApi('AQUI :'.$token);
        $page=Mk_forms::getParam('page', 1, $token);
        $perPage=Mk_forms::getParam('per_page', 5, $token);
        $sortBy=Mk_forms::getParam('sortBy', 'id', $token);
        $order=Mk_forms::getParam('order', 'desc', $token);
        $buscarA=Mk_forms::getParam('buscar', '', $token);
        $cols=$request->cols;
        $disabled=$request->disabled;


        $modelo=new $this->__modelo();
        $table=$modelo->getTable();

        $where=Mk_db::getWhere($buscarA);

        if ($disabled==1) {
            if ($where != '') {
                $where ='('. $where. ")and({$table}.status<>'0')";
            } else {
                $where ="({$table}.status<>'0')";
            }
        }

        $consulta=$this->__modelo::orderBy($sortBy, $order);

        if ($where!='') {
            $consulta = $consulta->whereRaw($where);
        }

        if ($perPage<0) {
            $perPage=_maxRowTable;
        }


        if (isset($modelo->_relaciones)) {
            $consulta = $consulta->with($modelo->_relaciones);
        }

        if ($cols!='') {
            $cols=explode(',', $cols);
            $cols=array_merge([$modelo->getKeyName()], $cols);
        } else {
            $cols=array_merge([$modelo->getKeyName()], $modelo->getFill());
        }

        //$consulta= $consulta->makeHidden(['grupos.pivot']);

        $datos = $consulta->paginate($perPage, $cols, 'page', $page);

        // if ($modelo->hidden) {
        //     $total=$datos['total'];
        //     $datos=$datos->makeHidden('datagrupos.pivot');
        //     $datos['total']=$total;
        // }

        if ($request->ajax()) {
            return  $datos;
        } else {
            $d=$datos->toArray($modelo->getHidden());
            $data = ['ok' => $d['total'], 'data' => $d['data']];
            return Mk_db::sendData($d['total'], $d['data'], '', $_debug);
        }
    }

    public function beforeDel($id, $modelo)
    {
    }

    public function afterDel($id, $modelo, $error=0)
    {
    }

    public function beforeSave(Request $request, $modelo, $action=1)
    {
    }

    public function afterSave(Request $request, $modelo, $error=0, $action=1)
    {
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $datos = new $this->__modelo();

            $this->beforeSave($request, $datos, 1);
            $datos->fill($request->except('paramsExtra'));
            //$datos->status = '1';
            $r=$datos->save();


            if ($r) {
                $r=$datos->id;
                $msg='';
                $this->afterSave($request, $datos, $r, 1);
                DB::commit();
            } else {
                DB::rollback();
                $r=_errorAlGrabar;
                $msg='Error Al Grabar';
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $r=_errorAlGrabar2;
            $msg="Error mientras se Grababa: \n".$th->getMessage(). "\n ************* \n ".$th;
        }

        if (!$request->ajax()) {
            return Mk_db::sendData($r, $this->index($request, false), $msg);
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
        try {
            $datos = new $this->__modelo();

            $this->beforeSave($request, $datos, 2);

            $r=$datos->where('id', '=', $id)
             ->update(
                 $request->except('paramsExtra')
             );
            $msg='';

            if ($r==0) {
                $r=_errorNoExiste;
                $msg='Registro ya NO EXISTE';
                DB::rollback();
            } else {
                $this->afterSave($request, $datos, $r, 2);
                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $r=_errorAlGrabar2;
            $msg='Error mientras se Actualizaba: '.$th->getMessage();
        }
        if (!$request->ajax()) {
            return Mk_db::sendData($r, $this->index($request, false), $msg);
        }
    }

    public function destroy(Request $request)
    {
        $id=explode(',', $request->id);
        DB::beginTransaction();
        try {
            $datos = new $this->__modelo();
            $this->beforeDel($id, $datos);
            $r=$datos->wherein('id', $id)
            ->delete();
            //TODO: hacer el sofdelete a las relaciones tambien
            $msg='';

            if ($r==0) {
                $r=_errorNoExiste;
                $msg='Registro ya NO EXISTE';
                DB::rollback();
            } else {
                $this->afterDel($id, $datos, $r);
                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $r=_errorAlGrabar2;
            $msg='Error mientras se Eliminaba: '.$th->getMessage();
        }

        if (!$request->ajax()) {
            return Mk_db::sendData($r, $this->index($request, false), $msg);
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
            return Mk_db::sendData($r, $this->index($request, false), $msg);
        }
    }
}
