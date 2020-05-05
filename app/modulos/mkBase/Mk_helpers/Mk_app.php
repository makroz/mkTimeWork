<?php
namespace App\modulos\mkBase\Mk_helpers;
use Illuminate\Support\Facades\Route;
class Mk_app
{
    public static function loadRoutes($path=''){
        if (empty($path)){
            $path=__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'modulos'.DIRECTORY_SEPARATOR;
            //echo $path;
            if (is_dir($path)) {
                if ($dh = opendir($path)) {
                    echo 'entro 1';
                   while (($file = readdir($dh)) !== false) {
                      //echo "<br>Nombre de archivo: $file : Es un: " . $path . $file;
                      if (is_dir($path . $file) && $file!="." && $file!=".."){
                        //echo "<br>Nombre de archivo: $file : Es un: " . $path . $file;
                          $routeFile=$path . $file.DIRECTORY_SEPARATOR.'Routes'.DIRECTORY_SEPARATOR.'route.php';
                          echo $routeFile;
                         if (file_exists($routeFile)){
                            require ($routeFile);
                         }
                      }
                   }
                closedir($dh);
                }
             }else{
                 echo "No es ruta valida";
             }


        }


    }
    public static function setRuta($modulo, $extras=[],$namespace='')
    {
        if (empty($extras['prefix'])) {
            $extras['prefix']=$modulo;
        }
        $prefix=$extras['prefix'];

        if (empty($extras['extras'])) {
            $extras['extras']=[];
        }
        $rutasExtras=$extras['extras'];
        Route::resource($prefix, $namespace. $modulo.'Controller');
        Route::group(['prefix' => $prefix], function () use ($modulo,$rutasExtras,$namespace) {
            Route::post('/delete',$namespace. $modulo.'Controller@destroy');
            Route::post('/restore', $namespace.$modulo.'Controller@restore');
            Route::post('/setStatus', $namespace.$modulo.'Controller@setStatus');
            foreach ($rutasExtras as $key => $lruta) {
                $method=$lruta[0];
                Route::{$method}($lruta[1], $namespace.$modulo.'Controller@'.$lruta[2]);
            }
        });
     }
}

//join(array_slice(explode("\\", $class), 0, -1), "\\"); consigue el namespace de una clase
