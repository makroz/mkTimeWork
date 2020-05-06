<?php

namespace App\modulos\mkEmpresas;

use Illuminate\Database\Eloquent\Model;
use \App\modulos\mkBase\Mk_ia_model;

class Empresas extends Model
{
    use Mk_ia_model;

    protected $fillable = ['name','email','status'];
    protected $attributes = ['status' => 1];

    // public $_withRelations = ['grupos'];
    // public $_pivot2Array = ['grupos'];
    // protected $cascadeDeletes = ['permisos','grupos'];

    public function getRules($request){
        return [
            'name' => 'required',
            'email' => 'required|email|unique:empresas,email,'.$request->input('id'),
            'status' => 'in:0,1'
        ];
    }
    public function sucursales()
    {
        return $this->belongsTo('App\modulos\mkEmpresas\Sucursales');
    }


}
