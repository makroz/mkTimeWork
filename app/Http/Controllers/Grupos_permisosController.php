<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Grupos_permisos;
use App\Help;
use Illuminate\Support\Facades\App;


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
        $npag=2;
        $buscar=$request->query('buscar','');
        $criterio=$request->query('criterio','');

        if ($buscar==''){
            $datos = Grupos_permisos::orderBy('id','desc')->paginate($npag);
        }else{
            $datos = Grupos_permisos::where($criterio,'like','%'.$buscar.'%')->orderBy('id','desc')->paginate($npag);
        }

        if ($request->ajax()) {
                        return  $datos;
        } else {
            if ($request->model=='grupos_permisos'){
                $d=$datos->toArray();
                $data = ['complete' => true, 'data' => $d['data'], 'message'=>'listado'];
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
        $datos = new Grupos_permisos();
        $datos->name = $request->name;
        $datos->status = '1';
        $datos->save();
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
        $datos = Grupos_permisos::findOrFail($id);
        $datos->name = $request->name;
        $datos->save();
    }

    public function destroy($id)
    {
        // TODO: Hacer el borrado de acuerdo si tiene una relacion o no
        $datos = Grupos_permisos::findOrFail($id);
        $datos->status = ($datos->status==1) ? 0 : 1 ;
        $datos->save();
    }
}
