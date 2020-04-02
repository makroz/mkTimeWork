<?php

namespace App\Mk_helpers;

use App\Mk_helpers\Mk_auth\Mk_auth;

use App\Mk_helpers\Mk_debug;
use Illuminate\Support\Facades\DB;

class Mk_db
{
    public static function startDbLog($force=false)
    {
        if ((Mk_debug::isDebugDb())or($force)) {
            DB::connection()->enableQueryLog();
            Mk_debug::setDebugDb(true);
        }
        return true;
    }

    public static function sendData($ok, $data=null, $msg='', $_debug=true)
    {
        $res = ['ok' => $ok];
        if ($data!==null) {
            $res['data']= $data;
        }
        if ($msg!='') {
            $res['msg']=$msg;
        }
        $token=(Mk_auth::get())->getToken();

        if ($_debug) {
            if ($token!=null) {
                (Mk_auth::get())->setToken(null);
                $res['_sid_']=$token;
            }
        }

        if ((Mk_debug::isDebug())&&($_debug)) {
            if (Mk_debug::isDebugDb()) {
                $res['_queryLog']=DB::getQueryLog();
            }
            if (Mk_debug::msgApi()>0) {
                $res['_debugMsg']=Mk_debug::getMsgApi();
            }
        }
        if (((Mk_auth::get())->getBlockData())){
            (Mk_auth::get())->blockData(false);
            $res['ok']=-1001;
            unset($res['data']);
            unset($res['_queryLog']);
            $res['msg']='No Autenticado';
        }
        return (response()->json($res))->original;
    }

    public static function getWhere($buscarA='')
    {
        $where='';
        //$crit=array();
        if ($buscarA!='') {
            $buscarA=json_decode($buscarA, true);
            $union='';
            foreach ($buscarA as $key => $value) {
                $value['campo']=DB::connection()->getPdo()->quote($value['campo']);
                $value['campo']=substr($value['campo'], 1, -1);

                $value['criterio']=DB::connection()->getPdo()->quote($value['criterio']);
                $value['criterio']=substr($value['criterio'], 1, -1);
                if ($value['criterio']!='') {
                    switch ($union) {
                        case 'and':
                            $where.='&&';
                            break;
                        case 'or':
                            $where.='||';
                            break;
                        default:
                            break;
                    }
                    $where.='(';

                    switch ($value['cond']) {
                        case  0: //igual
                        case 20:
                        case 40:
                            $where.=$value['campo']." = '".$value['criterio']."'";
                            break;
                        case  1: //diferente
                        case 21:
                        case 41:
                            $where.=$value['campo']." <> '".$value['criterio']."'";
                            break;

                        case 22: //mayor que
                        case 42:
                            $where.=$value['campo']." > '".$value['criterio']."'";
                            break;

                        case 23: //menor que
                        case 43:
                            $where.=$value['campo']." < '".$value['criterio']."'";
                            break;

                        case 24: //mayor o igual que
                        case 44:
                            $where.=$value['campo']." >= '".$value['criterio']."'";
                            break;

                        case 25: //menor o igual que
                        case 45:
                            $where.=$value['campo']." <= '".$value['criterio']."'";
                            break;

                        case 11: //contiene
                            $where.=$value['campo']." LIKE ('%".$value['criterio']."%')";
                            break;
                        case 12: //no contiene
                            $where.=$value['campo']." NOT LIKE ('%".$value['criterio']."%')";
                            break;

                        case 13: //empieza con
                            $where.=$value['campo']." LIKE ('%".$value['criterio']."')";
                            break;

                        case 14: //no empiezo comn
                            $where.=$value['campo']." NOT LIKE ('%".$value['criterio']."')";
                            break;

                        case 15: //termina por
                            $where.=$value['campo']." LIKE ('".$value['criterio']."%')";
                            break;

                        case 16: //no termina por
                            $where.=$value['campo']." NOT LIKE ('".$value['criterio']."%')'";
                            break;

                        default:
                            break;
                    }
                    $where.=')';
                    $union=$value['union'];
                }
            }
        }


        return $where;
    }
}
