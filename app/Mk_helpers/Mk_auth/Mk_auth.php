<?php
namespace App\Mk_helpers\Mk_auth;

use App\Mk_helpers\Mk_debug;
use \Request;
use \Cache;
class Mk_auth
{
    private $newToken = false;
    private $msgError = null;
    private $user=null;
    private $tipo='token';
    private $secret='sssawdsd8ws.6@';
    private $auth;
    private $blockData=false;
    private $coockie='c_sid';
    private $__modelo='\App\Usuarios';

    use \App\Mk_helpers\Mk_singleton;

    public function __construct($user=null)
    {
        //$this->model = new $modelo();
        define('__AUTH__', $this->tipo);
        define('__SECRET_KEY__', $this->secret);

        $this->auth  = FactoryAuth::getInstance();
        if (empty($user)) {
            $this->__init();
        } else {
            $this->setUser($user);
        }
        return true;
    }

    public function blockData($bloquear)
    {
        $this->blockData=$bloquear;
    }

    public function getBlockData()
    {
        return $this->blockData;
    }
    public static function get()
    {
        return Self::getInstance();
    }
    public function __init()
    {
        //$this->setToken($this->getToken(true));
        return true;
    }

    public function setUser($user=[],$campos=[])
    {
        $this->user=$user;
        if (empty($user)){
            $this->setToken(null);
        }else{
            $userToken=new \stdClass();
            $userToken->id=$user['id'];
            $userToken->name=$user['name'];
            foreach ($campos as $key => $value) {
                $userToken->$value=$user[$value];
            }
            $userToken->name=$user['name'];
            $userToken->rol=$user['rol'];
            $this->setToken($this->auth->autenticar($userToken));
        }

    }
    public function getUser()
    {
        return $this->user;
    }

    public function setToken($token=false)
    {
        $this->newToken=$token;

    }

    public function getTokenCoockie(){
        $token=$this->getToken();
        if (empty($token)){
            $token=$_COOKIE[$this->coockie];
            if (empty($token)) {
                $token=md5(date('ymd.').rand());
            }
        }else{
            $token=md5($token);
        }
        $_COOKIE[$this->coockie]=$token;
        //Mk_debug::msgApi($token);
        return $token;
    }

    public function getToken()
    {
        $token=$this->auth->getToken();
        return $token;
    }

    public function getNewToken()
    {
        $token=$this->newToken;
        $this->setToken(false);
        return $token;
    }
    public function isLogin()
    {

        try {
            return $this->auth->estaAutenticado();
        } catch (\Throwable $th) {
            $this->msgError=$th->getMessage().' >>'.$th->getFile().':'.$th->getLine();
            return false;
        }

    }


    public function permisosGruposMix($usuarios_id=0, $grupos_id=[], $debug=true)
    {
        $permisos = new \App\Permisos();
        $datos= $permisos->select('permisos.slug', \Illuminate\Support\Facades\DB::raw('BIT_OR(grupos_permisos.valor|usuarios_permisos.valor) as valor'))->leftJoin('usuarios_permisos', function ($join) use ($usuarios_id) {
            $join->on('permisos.id', '=', 'usuarios_permisos.permisos_id')
                 ->where('usuarios_id', '=', $usuarios_id);
        })->leftJoin('grupos_permisos', function ($join) use ($grupos_id) {
            $join->on('permisos.id', '=', 'grupos_permisos.permisos_id')
                 ->wherein('grupos_id', $grupos_id);
        })->groupBy('permisos.slug')->orderBy('permisos.name')->get();

        $d=$datos->toArray();
        return \App\Mk_helpers\Mk_db::sendData(count($d), $d, '', $debug);
    }

    public function login($username='',$password='',$id=0){
        $modelo=new $this->__modelo();
        if (empty($id)) {
            $datos=$modelo->select(['usuarios.id','usuarios.name','usuarios.email','usuarios.status','roles.id as rol_id','roles.name as rol'])->where('email', $username)->where('pass', $password)
        ->leftJoin('roles', 'roles.id', '=', 'roles_id')->with('grupos')->first();
        }else{
            $datos=$modelo->select(['usuarios.id','usuarios.name','usuarios.email','usuarios.status','roles.id as rol_id','roles.name as rol'])->where('usuarios.id', $id)
        ->leftJoin('roles', 'roles.id', '=', 'roles_id')->with('grupos')->first();
        }

        if (!$datos) {
            $user=[];
        } else {
            $user=$datos->toArray();
            $permisos=$this->permisosGruposMix($user['id'], $user['gruposid'], false);
            $user['permisos']=$permisos['data'];
        }
        $this->setUser($user);
        return $user;
    }

    public function canAccess($act='',$controller='')
    {
        $user=$this->auth->usuario();
        if (empty($user)){
            return false;
        }
        $router=Request::route()->getAction();
        $router=explode($router['namespace'].'\\', $router['controller']);
        $router=explode('Controller@', $router[1]);
        if (empty($controller)){
            $controller=$router[0];
        }
        if (empty($act)){
            $act=$router[1];
        }

        $user=Cache::remember('user', 240, function () use ($user) {//segundos
            Mk_debug::msgApi('entro');
            return $this->login(null,null,$user->id);
        });

        Mk_debug::msgApi($user);
        //TODO: AQUI me quede

    }

    public function getMsgError()
    {
        return $this->msgError;
    }

    public static function tokenPorCliente() {
        $aud = __SECRET_KEY__;

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud .= $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud .= $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud .= $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}

interface IAuth
{
    public function autenticar($usuario);
    public function estaAutenticado();
    public function destruir();
    public function usuario();

}

class FactoryAuth
{
    public static function getInstance()
    {
        //echo "Directorio:".\getcwd();
        $rut=sprintf('App\MK_helpers\Mk_auth\auth\%s\Auth', __AUTH__);
        //require_once sprintf(__DIR__.'\auth\%s\auth.php', __AUTH__);
        return new $rut();
    }
}
