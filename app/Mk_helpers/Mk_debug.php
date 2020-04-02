<?php

namespace App\Mk_helpers;

class Mk_debug
{
    private static $_mk_debug=-1;
    private static $_mk_debugDb=false;
    private static $msgApi=array();


    public static function __init()
    {
        self::$_mk_debug = env('IA_DEBUG', false);
        self::$_mk_debugDb=env('IA_DEBUG_DB', false);
    }

    public static function msgApi($msg='', $force=false)
    {
        $r=sizeof(self::$msgApi);
        if ($msg=='') {
            if ($r==0) {
                return false;
            }
            return $r;
        }
        $call=debug_backtrace(2,2);
        $call='   >> ['.basename($call[0]['file']).':'.$call[0]['line'].']';
        self::$msgApi[]=$msg.$call;

        return true;
    }

    public static function getMsgApi()
    {
        return self::$msgApi;
    }

    public static function isDebug()
    {
        if (self::$_mk_debug==-1) {
            self::__init();
        }
        return self::$_mk_debug;
    }

    public static function isDebugDb()
    {
        if (self::$_mk_debug==-1) {
            self::__init();
        }
        return self::$_mk_debugDb;
    }

    public static function setDebug($f=true)
    {
        self::$_mk_debug=$f;
        return true;
    }

    public static function setDebugDb($f=true)
    {
        self::$_mk_debugDb=$f;
        return true;
    }
}
