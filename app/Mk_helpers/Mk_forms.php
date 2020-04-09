<?php

namespace App\Mk_helpers;

use Request;
use Session;
use Cache;
use App\Mk_helpers\Mk_debug;
use App\Mk_helpers\Mk_auth\Mk_auth;

class Mk_forms
{
    public static $ses;
    public static $timeSession=86400;
    public function __construct()
    {
        $this->counter = 0;

        Mk_debug::msgApi('constructor form:');
    }

    public static function getSession($name, $default='')
    {
        $token=Mk_auth::get()->getTokenCoockie();
        if ($token=='') {
            return Session::get($name, $default);
        } else {
            return Cache::remember("{$token}.{$name}", self::$timeSession, function () use ($default) {
                return $default;
            });
            // $filename = self::$pathtoken.$token.'.tok';

            // if (file_exists($filename)) {
            //     $contents = json_decode(file_get_contents($filename), true);
            // } else {
            //     return $default;
            // }

            // if ((is_array($contents))and(array_key_exists($name, $contents))) {
            //     return $contents[$name];
            // } else {
            //     return $default;
            // }
        }
    }

    //TODO: leer una vez la sesion y guardarla en cache;

    public static function setSession($name, $value='',$time=86400)
    {
        $token=Mk_auth::get()->getTokenCoockie();
        //TODO:  llevar esto a otro helper de sesion o coquie
        if ($token=='') {
            Session::put($name, $value,$time);
        } else {
            Cache::put("{$token}.{$name}",$value,self::$timeSession);
            // $filename = self::$pathtoken.$token.'.tok';

            // if (file_exists($filename)) {
            //     $contents = json_decode(file_get_contents($filename), true);
            // } else {
            //     $contents=array();
            // }
            // if (!is_array($contents)) {
            //     $contents=array();
            // }
            // if (array_key_exists($name, $contents)) {
            //     $contents[$name]=$value;
            // } else {
            //     $contents = array_merge(array($name=>$value), $contents);
            // }

            // //$contents[$name]=$value;
            // file_put_contents($filename, json_encode($contents));
            // self::$counter=self::$counter+1;
            // if (self::$counter==1) {
            // }
        }
        return true;
    }
    public static function getParam($name, $default='')
    {
        $clase=Request::route()->getAction();
        $clase=explode($clase['namespace'].'\\', $clase['controller']);
        $clase=explode('Controller@', $clase[1]);
        $clase=$clase[0];
        $ruta="params.{$clase}.{$name}";
        if (Request::has($name)){
            $param=Request::input($name);
            self::setSession($ruta, $param);
        }else{
            $param=self::getSession($ruta, $default);
        }
        return $param;
    }

    public function __destruct()
    {
        Mk_debug::msgApi('Desconstructor form!!!:'.$this->counter);
    }
}
