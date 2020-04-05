<?php

namespace App\Mk_helpers;

use Request;
use Session;
use App\Mk_helpers\Mk_debug;
use App\Mk_helpers\Mk_auth\Mk_auth;

class Mk_forms
{
    public static $ses;
    public static $counter;
    public static $pathtoken='..'.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR;
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
            $filename = self::$pathtoken.$token.'.tok';

            if (file_exists($filename)) {
                $contents = json_decode(file_get_contents($filename), true);
            } else {
                return $default;
            }

            if ((is_array($contents))and(array_key_exists($name, $contents))) {
                return $contents[$name];
            } else {
                return $default;
            }
        }
    }

    //TODO:debe hacer unasola lectura del archivo tokem al ikniciar y uno al finalizar
    public static function setSession($name, $value='')
    {
        $token=Mk_auth::get()->getTokenCoockie();
        if ($token=='') {
            Session::put($name, $value);
        } else {
            $filename = self::$pathtoken.$token.'.tok';

            if (file_exists($filename)) {
                $contents = json_decode(file_get_contents($filename), true);
            } else {
                $contents=array();
            }
            if (!is_array($contents)) {
                $contents=array();
            }
            if (array_key_exists($name, $contents)) {
                $contents[$name]=$value;
            } else {
                $contents = array_merge(array($name=>$value), $contents);
            }

            //$contents[$name]=$value;
            file_put_contents($filename, json_encode($contents));
            self::$counter=self::$counter+1;
            if (self::$counter==1) {
            }
        }
        return true;
    }
    public static function getParam($name, $default='')
    {
        $name1=$name;
        $clase=Request::route()->getAction();
        $clase=explode($clase['namespace'].'\\', $clase['controller']);
        $clase=explode('Controller@', $clase[1]);
        $clase=$clase[0];
        $name=$clase.'_'.$name;
        $param=Request::input($name1, self::getSession($name, $default));
        //Mk_debug::msgApi(self::getSession($name, $default));
        self::setSession($name, $param);
        return $param;
    }

    public function __destruct()
    {
        Mk_debug::msgApi('Desconstructor form!!!:'.$this->counter);
    }
}
