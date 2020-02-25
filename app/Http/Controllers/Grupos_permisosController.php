<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Grupos_permisosController extends Controller
{
    use Mk_ia_db;

    private $__modelo='\App\Grupos_permisos';

    public function __construct(Request $request)
    {
        $this->__init($request);
        return true;
    }
}
