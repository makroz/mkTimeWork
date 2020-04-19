<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupos extends Model
{
    use Http\Controllers\Mk_ia_model;

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
        return $this->belongsToMany('App\Permisos','grupos_permisos')
        ->withPivot('valor');
    }
    public function usuarios()
    {
        return $this->belongsToMany('App\Usuarios','usuarios_grupos');
    }
}
