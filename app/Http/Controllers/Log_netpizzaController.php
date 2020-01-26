<?php

namespace App\Http\Controllers;

use Session;
use App\Log_netpizza;
use App\Mk_helpers\Mk_db;
use App\Mk_helpers\Mk_forms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Log_netpizzaController extends Controller
{
    public function __construct(Request $request)
    {
        Mk_db::startDbLog(true);
        //dd( "Controler : Grupo Permisos / Accion:".Help::getAction().'::::'.App::environment().'</hr>');
        return true;
    }


    public function dirToArray($dir)
    {
        $result = array();

        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".",".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }


    public function index(Request $request)
    {
        echo "<hr><hr><hr><hr><hr>Empeznaod de otra vez <hr><hr><hr>";
        $dir='/webs/laravel/gfactura/public/logfiles';
        $sucs=$this->dirToArray($dir);
        //print_r($files);
        $i=0;
        $total=0;
        $totfiles=0;
        foreach ($sucs as $suc => $files) {
            $totfiles=count($files);
            echo "<br>EMPEZANDO SUC $suc con Files: $totfiles<hr>";

            foreach ($files as $key0 => $file) {
                $i++;
                //if ($i==2) {
                $tam=filesize($dir.DIRECTORY_SEPARATOR.$suc.DIRECTORY_SEPARATOR.$file);

                $fp = fopen($dir.DIRECTORY_SEPARATOR.$suc.DIRECTORY_SEPARATOR.$file, "rb");
                $datos = fread($fp, $tam);
                fclose($fp);
                //$datos=str_replace('<hr><hr><hr>', '<br>---<br><br>---<br><br>---<br>', $datos);

                // echo "<hr> Revisando $file<hr>";
                // $ini=strpos($datos, "LOG='*****");
                // $p=1;
                // while ($ini!==false) {
                //     echo "<br>******** Si Tiene LOGS $p<br>";
                //     $fin=strpos($datos, "where PK", $ini);
                //     $uno=substr($datos, 0, $ini);
                //     $dos=substr($datos, $ini, $fin-$ini);
                //     $tres=substr($datos, $fin);
                //     $dos=str_replace('<hr>', '<br>---<br>', $dos);
                //     $datos=$uno.$dos.$tres;
                //     file_put_contents($dir.DIRECTORY_SEPARATOR.$suc.DIRECTORY_SEPARATOR.$file, $datos);
                //     $ini=strpos($datos, "LOG='*****", $fin);
                //     $p++;
                // }






                $datos=explode('<hr>', $datos);
                $total=$total+count($datos);
                echo "$suc : $file : $tam : lineas ". count($datos).'<hr>';
                $ini=0;
                $fin=1;
                foreach ($datos as $key1 => $dato) {
                    $ini=0;
                    if ($dato!='') {

                        //echo $dato.'<hr>';

                        $fin=strpos($dato, '(', $ini);
                        $fecha=substr($dato, $ini, $fin-$ini);
                        //$fecha=str_replace('/', '-', $fecha);
                        $fecha1 = explode(' ', $fecha);
                        $fecha = explode('/', $fecha1[0]);
                        $fecha=$fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.$fecha1[1];

                        //echo "Fecha: $fecha".$fecha1[0].'/'.$fecha1[1]." <br>";
                        //echo "(<strong>Fecha: $fecha ".print_r($fecha1, true)."</strong>)<br>";


                        $ini=$fin+1;

                        $fin=strpos($dato, ':', $ini);
                        $ip=substr($dato, $ini, $fin-$ini);
                        //echo "Ip: $ip <br>";
                        $ini=$fin+1;

                        $fin=strpos($dato, ') User', $ini);
                        $ruta=substr($dato, $ini, $fin-$ini);
                        //echo "ruta: $ruta <br>";
                        $ini=$fin+1;

                        $fin=strpos($dato, '-', $ini);
                        $user=substr($dato, $ini+6, $fin-$ini-6);
                        //echo "user: $user <br>";
                        $ini=$fin+1;

                        $fin=strpos($dato, '-', $ini);
                        $userD=substr($dato, $ini, $fin-$ini);
                        //echo "userDeescrip: $userD <br>";
                        $ini=$fin+1;

                        $fin=strpos($dato, '<br>', $ini);
                        $userId=substr($dato, $ini, $fin-$ini);
                        if (trim($userId)=='') {
                            $userId='0';
                        }
                        //echo "userId: $userId <br>";
                        $ini=$fin+1;

                        $comando=substr($dato, $fin+4);
                        //echo "comando: $comando ($suc)<hr> ";

                        $g = new Log_netpizza();
                        $g->fecha = $fecha;
                        $g->ip = $ip;
                        $g->ruta = $ruta;
                        $g->user = $user;
                        $g->user_desc = $userD;
                        $g->fk_user = $userId;
                        $g->mandato = $comando;
                        $g->suc = $suc;
                        $g->save();
                    }
                    //   }
                }
                echo "<hr>***********acabo con Files: $i";
                unlink($dir.DIRECTORY_SEPARATOR.$suc.DIRECTORY_SEPARATOR.$file);
                //redirect('public/Log_netpizza');
                echo "<script>location.reload();</script>";
                die();

                //head('location:http://gfactura.com/public/Log_netpizza');
            }
        }
    }

    public function store(Request $request)
    {
        $datos = new Grupos_permisos();
        $datos->name = $request->name;
        $datos->status = '1';
        $datos->save();

        if (!$request->ajax()) {
            return Mk_db::sendData($datos->id);
        }
    }

    public function show($id, Request $request)
    {
        $datos = Grupos_permisos::findOrFail($id);
        return $datos;
    }

    public function edit($id)
    {
        $datos = Grupos_permisos::findOrFail($id);
        return $datos;
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            $id=$request->id;
        }

        //$datos = Grupos_permisos::findOrFail($id);
        //$datos->name = $request->name;
        //$datos->save();

        $r=Grupos_permisos::where('id', '=', $id)
        ->update([
        'name' => $request->name,
        ]);

        if (!$request->ajax()) {
            return Mk_db::sendData($r);
        }
    }

    public function destroy($id)
    {
        // TODO: Hacer el borrado de acuerdo si tiene una relacion o no
        $datos = Grupos_permisos::findOrFail($id);
        $datos->status = 'X';
        $datos->save();
    }

    public function destroyapi(Request $request)
    {
        // TODO: Hacer el borrado de acuerdo si tiene una relacion o no
        $id=explode(',', $request->id);
        $r=Grupos_permisos::wherein('id', $id)
        ->update([
        'status' => 'X',
        ]);
        if (!$request->ajax()) {
            return Mk_db::sendData($r);
        }
    }
}
