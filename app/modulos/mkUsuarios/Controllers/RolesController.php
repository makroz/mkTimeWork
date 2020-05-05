<?php

namespace App\modulos\mkUsuarios\Controllers;
use App\modulos\mkBase\Controller;
use App\modulos\mkBase\Mk_ia_db;
use Illuminate\Http\Request;

class RolesController extends controller
{
    use Mk_ia_db;

    private $__modelo='\App\modulos\mkUsuarios\Roles';
    public function __construct(Request $request)
    {
        $this->__init($request);
        return true;
    }
}
