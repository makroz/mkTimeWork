<?php

namespace App\modulos\mkEmpresas\Controllers;

use App\modulos\mkBase\Controller;
use App\modulos\mkBase\Mk_ia_db;
use Illuminate\Http\Request;
use App\modulos\mkBase\Mk_Helpers\Mk_Auth\Mk_Auth;
use App\modulos\mkBase\Mk_Helpers\Mk_db;

class SucursalesController extends Controller
{
    use Mk_ia_db;
    //public $_autorizar='';

    private $__modelo='App\modulos\mkEmpresas\Sucursales';

    public function __construct(Request $request)
    {
        $this->__init($request);
        return true;
    }
}
