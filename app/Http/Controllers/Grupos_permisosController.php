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

    public function index(Request $request)
    {
        $datos = Grupos_permisos::paginate();
        if ($request->ajax()) {
            return  $datos;
        } else {
            return view('principal');
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
        dd("editar $id");
        $datos = Grupos_permisos::findOrFail($id);
        return $datos;
    }

    public function update(Request $request, $id)
    {
        $datos = Grupos_permisos::findOrFail($id);
        $datos->name = $request->name;
        $datos->save();
    }

    public function status($id)
    {
        $datos = Grupos_permisos::findOrFail($id);
        $datos->status = $status;
        $datos->save();
    }
}
