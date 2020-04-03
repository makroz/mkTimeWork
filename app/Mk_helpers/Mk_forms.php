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

    public static function getSession($name, $default='', $token='')
    {
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
    public static function setSession($name, $value='', $token='')
    {
        $token=Mk_auth::get()->getToken(true);
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
    public static function getParam($name, $default='', $token='')
    {
        $name1=$name;
        $clase=Request::route()->getAction();
        //print_r($clase);
        $clase=explode($clase['namespace'].'\\', $clase['controller']);
        $clase=explode('Controller@', $clase[1]);

        $clase=$clase[0];

        $name=$clase.'_'.$name;
        $param=Request::input($name1, self::getSession($name, $default, $token));
        //Mk_debug::msgApi($name.':'. $param);

        self::setSession($name, $param, $token);
        //Mk_debug::msgApi($name.'2:'. self::getSession($name, 'x', $token));
        //Mk_debug::msgApi($clase.'_'.$name.'2:'. $_SESSION[$clase.'_'.$name]);
        return $param;
    }

    public function __destruct()
    {
        Mk_debug::msgApi('Desconstructor form!!!:'.$this->counter);
    }
}
