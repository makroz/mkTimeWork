<?php

namespace App\modulos\mkBase;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;


class Controller extends BaseController
{
    use  DispatchesJobs, ValidatesRequests; //TODO::aumentar trait para injectar las Authorizaciones
}
