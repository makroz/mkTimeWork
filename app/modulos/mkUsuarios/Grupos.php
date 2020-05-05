<?php

namespace App\modulos\mkUsuarios;

use Illuminate\Database\Eloquent\Model;
use \App\modulos\mkBase\Mk_ia_model;

class Grupos extends Model
{
    use Mk_ia_model;

    protected $fillable = ['name', 'descrip','status'];
    protected $attributes = ['status' => 1];
    protected $cascadeDeletes = ['usuarios','permisos'];

    public function getRules($request){
        return [
        'name' => 'required',
        'status' => 'in:0,1'
        ];
    }

    public function permisos()
    {
        return $this->belongsToMany('App\modulos\mkUsuarios\Permisos','grupos_permisos')
        ->withPivot('valor');
    }
    public function usuarios()
    {
        return $this->belongsToMany('App\modulos\mkUsuarios\Usuarios','usuarios_grupos');
    }
}
