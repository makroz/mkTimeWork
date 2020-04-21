<?php
namespace App\Mk_helpers;
use Illuminate\Support\Facades\Route;
class Mk_app
{
    public static function setRuta($modulo, $extras=[])
    {
        if (empty($extras['prefix'])) {
            $extras['prefix']=$modulo;
        }
        $prefix=$extras['prefix'];

        if (empty($extras['extras'])) {
            $extras['extras']=[];
        }
        $rutasExtras=$extras['extras'];
        Route::resource($prefix, $modulo.'Controller');
        Route::group(['prefix' => $prefix], function () use ($modulo,$rutasExtras) {
            Route::post('/delete', $modulo.'Controller@destroy');
            Route::post('/restore', $modulo.'Controller@restore');
            Route::post('/setStatus', $modulo.'Controller@setStatus');
            foreach ($rutasExtras as $key => $lruta) {
                $method=$lruta[0];
                Route::{$method}($lruta[1], $modulo.'Controller@'.$lruta[2]);
            }
        });
     }
}

