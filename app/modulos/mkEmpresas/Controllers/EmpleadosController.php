<?php

namespace App\modulos\mkEmpresas\Controllers;

use App\modulos\mkBase\Controller;
use App\modulos\mkBase\Mk_ia_db;
use Illuminate\Http\Request;
use App\modulos\mkBase\Mk_Helpers\Mk_Auth\Mk_Auth;
use App\modulos\mkBase\Mk_Helpers\Mk_db;

class EmpleadosController extends Controller
{
    use Mk_ia_db;
    //public $_autorizar='';

    private $__modelo='App\modulos\mkEmpresas\Empleados';

    public function __construct(Request $request)
    {
        $this->__init($request);
        return true;
    }
}
