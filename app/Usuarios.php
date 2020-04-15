<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    use Http\Controllers\Mk_ia_model;

    protected $fillable = ['name','email','pass', 'roles_id','status'];
    protected $attributes = ['status' => 1];
    protected $hidden = ['pass'];

    public $_validators =[
        'name' => 'required',
        'email' => 'required|email',
        'pass' => 'sometimes|required|min:8',
        'roles_id' => 'integer',
        'status' => 'in:0,1'
    ];

    public $_withRelations = ['grupos'];
    public $_pivot2Array = ['grupos'];
    protected $cascadeDeletes = ['permisos','grupos'];

    public function permisos()
    {
        return $this->belongsToMany('App\Permisos', 'usuarios_permisos')
        ->withPivot('valor');
    }

    public function grupos()
    {
        return $this->belongsToMany('App\Grupos', 'usuarios_grupos');

    }

    public function roles()
    {
        return $this->hasMany('App\Roles');
    }

}
