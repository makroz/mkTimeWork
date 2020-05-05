<?php

namespace App\modulos\mkUsuarios\Controllers;
use App\modulos\mkBase\Controller;
use App\modulos\mkBase\Mk_ia_db;
use Illuminate\Http\Request;

class PermisosController extends Controller
{
    use Mk_ia_db;

    private $__modelo='\App\modulos\mkUsuarios\Permisos';
    public function __construct(Request $request)
    {
        $this->__init($request);
        return true;
    }
}
