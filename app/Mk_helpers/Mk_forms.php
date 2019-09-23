<?php

namespace App\Mk_helpers;

use Request;
use Session;
use App\Mk_helpers\Mk_debug;

class Mk_forms
{
    public static function getParam($name, $default='')
    {
        $clase=Request::route()->getAction();
        $clase=$clase['as'];

        $param=Request::input($name, Session::get($clase.'_'.$name, $default));
        Session::put($clase.'_'.$name, $param);
        //Mk_debug::msgApi($clase.'_'.$name.':'. $param);
        return $param;
    }
}
