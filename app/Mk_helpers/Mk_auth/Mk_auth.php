<?php


namespace App\Mk_helpers\Mk_auth;

class Mk_auth
{
    private $token = null;
    private $msgError = null;
    private $user=null;
    private $tipo='token';
    private $secret='sssawdsd8ws.6@';
    private $auth;
    private $blockData=false;

    use \App\Mk_helpers\Mk_singleton;

    public function __construct($user=null)
    {
        //$this->model = new $modelo();
        define('__AUTH__', $this->tipo);
        define('__SECRET_KEY__', $this->secret);

        $this->auth  = FactoryAuth::getInstance();
        if (!$user) {
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
        $this->setToken($this->getToken(true));
        return true;
    }

    public function setUser($user=null)
    {
        $this->user=$user;
        if ($user==null){
            $this->setToken(null);
        }else{
            $this->setToken($this->auth->autenticar($user));
        }

    }
    public function getUser()
    {
        return $this->user;
    }

    public function setToken($token='')
    {
        $this->token=$token;

    }

    public function getToken($force=false)
    {
        $token=$this->token;
        // if ((empty($token))&&($force)){
        //         $token=date('ymd.').rand();
        //         $this->token=$token;
        // }
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
