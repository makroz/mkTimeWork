<?php

namespace App\modulos\mkUsuarios;

use Illuminate\Database\Eloquent\Model;
use \App\modulos\mkBase\Mk_ia_model;

class Permisos extends Model
{
    use Mk_ia_model;

    protected $fillable = ['name','slug', 'descrip','status'];
    protected $attributes = ['status' => 1];
    protected $cascadeDeletes = ['usuarios','grupos'];

    public function getRules($request){
        return [
        'name' => 'required',
        'slug' => 'required|unique:permisos,slug,'.$request->input('id'),
        'status' => 'in:0,1'
    ];
    }

    public function usuarios()
    {
        return $this->belongsToMany('App\modulos\mkUsuarios\Usuarios', 'usuarios_permisos')
        ->withPivot('valor');
    }
    public function grupos()
    {
        return $this->belongsToMany('App\modulos\mkUsuarios\Grupos', 'grupos_permisos')
        ->withPivot('valor');
    }


}
