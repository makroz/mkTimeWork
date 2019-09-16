<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Grupos_permisos;
use App\Help;
use Illuminate\Support\Facades\App;
use Session;


class Grupos_permisosController extends Controller
{

    public function __construct()
    {

        //dd( "Controler : Grupo Permisos / Accion:".Help::getAction().'::::'.App::environment().'</hr>');
        return true;
    }

        public function api(Request $request){
            $data = ['complete' => true, 'data' => ['algo'], 'message'=>'todo bien aca'];
            return response()->json($data);
        }

    public function index(Request $request)
    {
        $page=$request->post('page',$request->get('page',1));
        $perPage=$request->post('per_page',$request->get('per_page',Session::get('per_page', 5)));
        $sortBy=$request->post('sortBy',$request->get('sortBy',Session::get('sortBy', 'id')));
        $order=$request->post('ordorder',$request->get('order',Session::get('order', 'desc')));
        $buscar=$request->query('buscar','');
        $criterio=$request->query('criterio','');

        Session::put('per_page', $perPage);
        Session::put('sortBy', $sortBy);
        Session::put('order', $order);

        if ($buscar==''){
            $datos = Grupos_permisos::orderBy($sortBy,$order)->paginate($perPage, ['*'], 'page', $page);
        }else{
            $datos = Grupos_permisos::where($criterio,'like','%'.$buscar.'%')->orderBy($sortBy,$order)->paginate($perPage ,['*'], 'page', $page);
        }

        if ($request->ajax()) {
                        return  $datos;
        } else {
            $d=$datos->toArray();
            if ($request->model=='grupos_permisos'){//omar
                $data = ['complete' => true, 'data' => $d['data'], 'message'=>'listado', 'total'=>$d['total']];
            }else{//mariio
                $data = ['ok' => $d['total'], 'data' => $d['data']];
            }
            return response()->json($data);
        }
    }

    public function create()
    {

        //$datos = Grupos_permisos::findOrFail($id);
        return $datos;
    }

    public function store(Request $request)
    {
        $datos = new Grupos_permisos();
        $datos->name = $request->name;
        $datos->status = '1';
        $datos->save();

        if (!$request->ajax()) {
            $data = ['complete' => $datos->id];
            return response()->json($data);
        }
    }

    public function show($id, Request $request)
    {
        $datos = Grupos_permisos::findOrFail($id);
        return $datos;
    }

    public function edit($id)
    {
        //dd("editar $id");
        $datos = Grupos_permisos::findOrFail($id);
        return $datos;
    }

    public function update(Request $request, $id)
    {
        if (!$id){
            $id=$request->id;
        }
        $datos = Grupos_permisos::findOrFail($id);
        $datos->name = $request->name;
        $datos->save();
        if (!$request->ajax()) {
            $data = ['complete' => $datos->id];
            return response()->json($data);
        }
    }

    public function destroy($id)
    {
        // TODO: Hacer el borrado de acuerdo si tiene una relacion o no
        $datos = Grupos_permisos::findOrFail($id);
        $datos->status = ($datos->status==1) ? 0 : 1 ;
        $datos->save();
    }

    public function destroyapi(Request $request)
    {
            $id=$request->id;

        $datos = Grupos_permisos::findOrFail($id);
        $datos->status = ($datos->status==1) ? 0 : 1 ;
        $datos->save();
        if (!$request->ajax()) {
            $data = ['complete' => $datos->id];
            return response()->json($data);
        }
    }

}
