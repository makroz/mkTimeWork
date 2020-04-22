<?php
namespace App\Http\Controllers;

use \App\Mk_helpers\Mk_db;
use \App\Mk_helpers\Mk_debug;
use \App\Mk_helpers\Mk_forms;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Mk_helpers\Mk_auth\Mk_auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

const _maxRowTable=1000;
const _errorNoExiste=-1;
const _errorAlGrabar=-10;
const _errorAlGrabar2=-11;
const _errorLogin=-1000;
const _errorNoAutenticado=-1001;
const _cachedQuerys='cachedQuerys_';




trait Mk_ia_db
{
    public function proteger($act='',$controler=''){

        if (isset($this->_autorizar)) {
            Mk_debug::msgApi(['proteger y autorizar',$this->_autorizar]);
            if (empty($controler)){
                if (!empty($this->_autorizar)){
                    $controler=$this->_autorizar;
                }

            }

            Mk_auth::get()->proteger($act,$controler);
        }
    }
    public function __init(Request $request)
    {
        Mk_db::startDbLog();
        return true;
    }
    public function index(Request $request, $_debug=true)
    {
        $this->proteger();
        $page=Mk_forms::getParam('page', 1);
        $perPage=Mk_forms::getParam('per_page', 5);
        $sortBy=Mk_forms::getParam('sortBy', 'id');
        $order=Mk_forms::getParam('order', 'desc');
        $buscarA=Mk_forms::getParam('buscar', '');
        $recycled=$request->recycled;
        $cols=$request->cols;
        $disabled=$request->disabled;

        $prefix=$this->addCacheList([$this->__modelo,$page,$perPage,$sortBy,$order,$buscarA,$recycled,$cols,$disabled]);

        $datos=Cache::remember($prefix, 60*60*24, function () use ($prefix,$page,$perPage,$sortBy,$order,$buscarA,$recycled,$cols,$disabled) {
            $modelo=new $this->__modelo();
            $table=$modelo->getTable();
            $consulta=$modelo->orderBy($sortBy, $order);

            $where=Mk_db::getWhere($buscarA);

            if ($recycled==1){
                $consulta=$consulta->onlyTrashed();
            }

            if ($disabled==1) {
                if ($where != '') {
                    $where ='('. $where. ")and({$table}.status<>'0')";
                } else {
                    $where ="({$table}.status<>'0')";
                }
            }

            if ($where!='') {
                $consulta = $consulta->whereRaw($where);
            }

            if ($perPage<0) {
                $perPage=_maxRowTable;
            }


            if (isset($modelo->_withRelations)) {
                    $consulta = $consulta->with($modelo->_withRelations);
            }

            if ($cols!='') {
                $cols=explode(',', $cols);
                $cols=array_merge([$modelo->getKeyName()], $cols);
            } else {
                $cols=array_merge([$modelo->getKeyName()], $modelo->getFill());
            }
            Mk_debug::msgApi('Se añadio Item Cache:'.$prefix);
            return $consulta->paginate($perPage, $cols, 'page', $page);
        });

        if ($request->ajax()) {
            return  $datos;
        } else {
            $d=$datos->toArray();
            $data = ['ok' => $d['total'], 'data' => $d['data']];
            return Mk_db::sendData($d['total'], $d['data'], '', $_debug);
        }
    }

    public function beforeDel($id, $modelo){}
    public function afterDel($id, $modelo, $error=0){}

    public function beforeRestore($id, $modelo){}
    public function afterRestore($id, $modelo, $error=0){}

    public function beforeSave(Request $request, $modelo, $action=1){}
    public function afterSave(Request $request, $modelo, $error=0, $action=1){}

    public function store(Request $request)
    {
        $this->proteger();
        DB::beginTransaction();
        try {
            $datos = new $this->__modelo();
            $rules=$datos->getRules($request);
            if (!empty($rules)){
                $validatedData = $request->validate($rules);
            }

            $datos->fill($request->only($datos->getfill()));
            $this->beforeSave($request, $datos, 1);
            $r=$datos->save();


            if ($r) {
                $r=$datos->id;
                $msg='';
                $this->afterSave($request, $datos, $r, 1);
                DB::commit();
                $this->clearCache();
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
        try {
            $this->proteger();
        $datos= new $this->__modelo;
        $key=$datos->getKeyName();
        $datos = $datos->where(str_replace("'", "",DB::connection()->getPdo()->quote( $request->where)),
                                str_replace("'", "",DB::connection()->getPdo()->quote( $request->valor)))
                        ->where($datos->getKeyName(),'!=',$id);
        if (empty($request->existe)){
            $datos = $datos->first();
            if (!$datos){
                $id=-1;
            }else{
                $id=$datos->$key;
            }
            return Mk_db::sendData($id, $datos);
        }else{
            $datos = $datos->select($key)->first();
            if (!$datos){
                $id=-1;
            }else{
                $id=$datos->$key;
            }

            return Mk_db::sendData($id);
        }
        } catch (\Throwable $th) {
            return Mk_db::sendData(-2,null,$th->getMessage());
        }
    }

    public function edit($id)
    {
        $this->proteger();
        $datos = $this->__modelo::findOrFail($id);
        return $datos;
    }

    public function update(Request $request, $id)
    {
        $this->proteger();
        if (!$id) {
            $id=$request->id;
        }
        DB::beginTransaction();
        try {
            $datos = new $this->__modelo();
            $rules=$datos->getRules($request);
            if (!empty($rules)){
                $validatedData = $request->validate($rules);
            }
            $this->beforeSave($request, $datos, 2);
            $r=$datos->where('id', '=', $id)
             ->update(
                 $request->only($datos->getfill())
             );
            $msg='';
            if ($r==0) {
                $r=_errorNoExiste;
                $msg='Registro ya NO EXISTE';
                DB::rollback();
            } else {
                $this->afterSave($request, $datos, $r, 2);
                DB::commit();
                $this->clearCache();
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $r=_errorAlGrabar2;
            $msg='Error mientras se Actualizaba: '.$th->getLine().':'.$th->getFile().'='.$th->getMessage();
        }
        if (!$request->ajax()) {

            return Mk_db::sendData($r, $this->index($request, false), $msg);
        }
    }

    public function destroy(Request $request)
    {
        $this->proteger();
        $recycled=$request->recycled;
        $id=explode(',', $request->id);
        DB::beginTransaction();
        try {
            $datos = new $this->__modelo();
            $this->beforeDel($id, $datos);
            if ($recycled==1){
                $r=$datos->onlyTrashed()->wherein('id', $id)
                ->forceDelete();;
            }else{
                $datos->runCascadingDeletes($id);
                $r=$datos->wherein('id', $id)
                ->delete();
            }
            $msg='';
            if ($r==0) {
                $r=_errorNoExiste;
                $msg='Registro ya NO EXISTE';
                DB::rollback();
            } else {
                $this->afterDel($id, $datos, $r);
                DB::commit();
                $this->clearCache();
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

    public function restore(Request $request)
    {
        $this->proteger();
        $recycled=$request->recycled;
        $id=explode(',', $request->id);

        DB::beginTransaction();
        try {
            if ($recycled!=1) {
                throw new Exception("Debe estar en Papelera de Reciclaje", 1);
            }
            $datos = new $this->__modelo();
            $this->beforeRestore($id, $datos);
                $datos->runCascadingDeletes($id,true);
                $r=$datos->onlyTrashed()->wherein('id', $id)
                ->restore();
            $msg='';
            if ($r==0) {
                $r=_errorNoExiste;
                $msg='Registro ya NO EXISTE';
                DB::rollback();
            } else {
                $this->afterRestore($id, $datos, $r);
                DB::commit();
                $this->clearCache();
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $r=_errorAlGrabar2;
            $msg='Error mientras se Restauraba: '.$th->getMessage();
        }

        if (!$request->ajax()) {
            return Mk_db::sendData($r, $this->index($request, false), $msg);
        }
    }

    public function setStatus(Request $request)
    {
        $this->proteger();
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
            $this->clearCache();
            return Mk_db::sendData($r, $this->index($request, false), $msg);
        }
    }

    private function addCacheList($key=['ok']){
        $prefixList=_cachedQuerys.$this->__modelo;
        $prefix=md5(collect($key)->__toString());
        $cached=Cache::get($prefixList,[]);
        if (!in_array($prefix,$cached)){
            $cached[] = $prefix;
        }
        Cache::add($prefixList,$cached);
        Mk_debug::msgApi(['Cache Lista Añadido: '.$prefix,$cached]);
        return $prefix;
    }

    private function clearCache(){
        $prefixList=_cachedQuerys.$this->__modelo;
        $cached=Cache::get($prefixList,[]);
        Mk_debug::msgApi(['se limpia cache de: '.$prefixList,$cached]);
        foreach ($cached as $key => $value) {
            Cache::forget($value);
            Mk_debug::msgApi('limpiando '.$value);
        }
        Cache::forget($prefixList,[]);
        Mk_debug::msgApi(['se limpio '.$prefixList,Cache::get($prefixList,'Vacio')]);
        return true;
    }
}
