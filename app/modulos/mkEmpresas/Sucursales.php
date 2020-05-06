<?php

namespace App\modulos\mkEmpresas;

use Illuminate\Database\Eloquent\Model;
use \App\modulos\mkBase\Mk_ia_model;

class Sucursales extends Model
{
    use Mk_ia_model;

    protected $fillable = ['name','dir','email','tel','status','empresas_id'];
    protected $attributes = ['status' => 1];

     public $_withRelations = ['empresas'];
    // public $_pivot2Array = ['grupos'];
    // protected $cascadeDeletes = ['permisos','grupos'];

    public function getRules($request){
        return [
            'name' => 'required',
            'email' => 'required|email|unique:sucursales,email,'.$request->input('id'),
            'status' => 'in:0,1'
        ];
    }

    public function empresas()
    {
        return $this->hasOne('App\modulos\mkEmpresas\Empresas');
    }
    public function empleados()
    {
        return $this->belongsTo('App\modulos\mkEmpresas\Empleados');
    }

}
