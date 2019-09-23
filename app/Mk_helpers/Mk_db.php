<?php

namespace App\Mk_helpers;

use App\Mk_helpers\Mk_debug;
use Illuminate\Support\Facades\DB;

class Mk_db
{
    public static function startDbLog($force=false)
    {
        if ($force) {
            Mk_debug::init(true);//debe ir en env
        }

        if (Mk_debug::isDebug()) {
            DB::connection()->enableQueryLog();
        }
        return true;
    }

    public static function sendData($ok, $data=null, $msg='')
    {
        $res = ['ok' => $ok];
        if ($data!==null) {
            $res['data']= $data;
        }
        if ($msg!='') {
            $res['msg']=$msg;
        }
        if (Mk_debug::isDebug()) {
            $res['_queryLog']=DB::getQueryLog();
            if (Mk_debug::msgApi()>0) {
                $res['_debugMsg']=Mk_debug::getMsgApi();
            }
        }

        return response()->json($res);
    }
}
