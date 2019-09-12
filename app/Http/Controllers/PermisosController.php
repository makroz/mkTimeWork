<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Permisos;
use App\Help;
use Illuminate\Support\Facades\App;
use Session;


class PermisosController extends Controller
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
        $page=$request->post('page',$request->post('page',$request->get('page',1)));
        $npag=$request->post('per_page',Session::get('per_page', 10));
        $buscar=$request->query('buscar','');
        $criterio=$request->query('criterio','');
        Session::put('per_page', $npag);

        if ($buscar==''){
            $datos = Permisos::orderBy('id','desc')->paginate($npag, ['*'], 'page', $page);
        }else{
            $datos = Permisos::where($criterio,'like','%'.$buscar.'%')->orderBy('id','desc')->paginate($npag ,['*'], 'page', $page);
        }

        if ($request->ajax()) {
                        return  $datos;
        } else {
            if ($request->model=='permisos'){
                $d=$datos->toArray();
                $data = ['complete' => true, 'data' => $d['data'], 'message'=>'listado', 'total'=>$d['total']];
                return response()->json($data);
            }
        }
    }

    public function create()
    {

        //$datos = Grupos_permisos::findOrFail($id);
        return $datos;
    }

    public function store(Request $request)
    {
        $datos = new Permisos();
        $datos->name = $request->name;
        $datos->fk_grupospermisos = $request->fk_grupospermisos;
        $datos->status = '1';
        $datos->save();

        if (!$request->ajax()) {
            $data = ['complete' => $datos->id];
            return response()->json($data);
        }
    }

    public function show($id, Request $request)
    {
        $datos = Permisos::findOrFail($id);
        return $datos;
    }

    public function edit($id)
    {
        //dd("editar $id");
        $datos = Permisos::findOrFail($id);
        return $datos;
    }

    public function update(Request $request, $id)
    {
        if (!$id){
            $id=$request->id;
        }
        $datos = Permisos::findOrFail($id);
        $datos->name = $request->name;
        $datos->fk_grupospermisos = $request->fk_grupospermisos;
        $datos->save();
        if (!$request->ajax()) {
            $data = ['complete' => $datos->id];
            return response()->json($data);
        }
    }

    public function destroy($id)
    {
        // TODO: Hacer el borrado de acuerdo si tiene una relacion o no
        if (!$id){
            $id=$request->id;
        }

        $datos = Permisos::findOrFail($id);
        $datos->status = ($datos->status==1) ? 0 : 1 ;
        $datos->save();
        if (!$request->ajax()) {
            $data = ['complete' => $datos->id];
            return response()->json($data);
        }
    }
}
