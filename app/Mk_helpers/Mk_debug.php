<?php

namespace App\Mk_helpers;

class Mk_debug
{
    private static $_mk_debug=false;
    private static $msgApi=array();

    public static function msgApi($msg='')
    {
        $r=sizeof(self::$msgApi);
        if ($msg=='') {
            if ($r==0) {
                return false;
            }
        }
        self::$msgApi[]=$msg;
        return $r+1;
    }

    public static function getMsgApi()
    {
        return self::$msgApi;
    }

    public static function isDebug()
    {
        return self::$_mk_debug;
    }
    public static function init($f=true)
    {
        self::$_mk_debug=$f;
        return true;
    }
}
